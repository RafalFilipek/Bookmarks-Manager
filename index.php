<?php

require_once __DIR__.'/silex.phar';

$app = new Silex\Application();

$app['autoloader']->registerNamespaces(array(
    'Symfony'   => __DIR__.'/vendor/symfony/src',
    'Rafal'     => __DIR__.'/vendor/rafal/src'
));

$app['debug'] = true;

$app->register(new Rafal\MemcacheExtension\MemcacheExtension());

$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path'       => __DIR__.'/views',
    'twig.class_path' => __DIR__.'/vendor/twig/lib',
    'twig.options'    => array(
        'caches' => __DIR__.'/cache/twig'
    )
));

$app->register(new Rafal\ProfilerExtension\ProfilerExtension(), array(
    'profiler.data_url' => '__fetch_profiler_data',
    'profiler.cookie_name' => 'web_profiler'
));

$app->register(new Silex\Extension\UrlGeneratorExtension());

$app->register(new Silex\Extension\DoctrineExtension(), array(
    'db.options'    => array(
        'dbname'    => 'bm',
        'host'      => '127.0.0.1',
        'user'      => 'root',
        'password'  => ''
    ),
    'db.dbal.class_path'    => __DIR__.'/vendor/doctrine-dbal/lib',
    'db.common.class_path'  => __DIR__.'/vendor/doctrine-common/lib'
));

$app->register(new Silex\Extension\TranslationExtension(), array(
    'locale'                    => 'en',
    'locale_fallback'           => 'pl',
    'translation.class_path'    => __DIR__.'/vendor/symfony/src',
    'translator.messages'       => array(
        'pl' => __DIR__.'/locales/pl.yml',
        'en' => __DIR__.'/locales/en.yml'
    )
));

$app['translator.loader'] = new Symfony\Component\Translation\Loader\YamlFileLoader();

$app->get('/', function() use ($app) {
    $categories = $app['memcache']->get('categories-list', function() use($app) {
        return $app['db']->fetchAll('
            SELECT c.*, COUNT(bc.category_id) AS count 
            FROM Category c, bookmark_category bc 
            WHERE bc.category_id = c.id 
            GROUP BY c.id 
            ORDER BY c.name ASC
        ');
    });
    $groupedCategories = array('marked' => array(), 'normal' => array());
    foreach ($categories as $category) {
        $groupedCategories[$category['marked'] == 1 ? 'marked' : 'normal'][] = $category;
    }
    return $app['twig']->render('index.twig', array(
        'categories' => $groupedCategories
    ));
});

/**
 * @TODO - zmienić na POST
 */
$app->get($app['translator']->trans('routing.set_marked.url'), function($id, $bool) use ($app) {
    $count = $app['db']->executeUpdate('UPDATE Category SET marked = ? WHERE ID = ?', array($bool, $id));
    $app['memcache']->delete('categories-list');
    return json_encode($count == 1);
})
    ->assert('id', '\d+')
    ->assert('bool', $app['translator']->trans('routing.set_marked.params.yes_no'))
    ->convert('bool', function($v) use($app) { return strtolower($v) == $app['translator']->trans('routing.set_marked.params.yes') ? true : false; });

$app->run();