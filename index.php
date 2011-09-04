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
    'locale'                    => 'pl',
    'locale_fallback'           => 'pl',
    'translation.class_path'    => __DIR__.'/vendor/symfony/src',
    'translator.messages'       => array(
        'pl' => __DIR__.'/locales/pl.yml',
        'en' => __DIR__.'/locales/en.yml'
    )
));

$app['translator.loader'] = new Symfony\Component\Translation\Loader\YamlFileLoader();

$app->get('/', function() use ($app) {
    $categories = $app['db']->fetchAll('SELECT * FROM Category ORDER BY name ASC');
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
    return json_encode($count == 1);
})
    ->assert('id', '\d+')
    ->assert('bool', $app['translator']->trans('routing.set_marked.params.yes_no'))
    ->convert('bool', function($v) use($app) { return strtolower($v) == $app['translator']->trans('routing.set_marked.params.yes') ? true : false; });

$app->run();
