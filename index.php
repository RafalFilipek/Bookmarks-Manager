<?php

require_once __DIR__.'/silex.phar';

$app = new Silex\Application();

$app['debug'] = true;

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
$app->get('/kategorie/{id}/ustaw-zaznaczenie/{bool}', function($id, $bool) use ($app) {
    $count = $app['db']->executeUpdate('UPDATE Category SET marked = ? WHERE ID = ?', array($bool, $id));
    return json_encode($count == 1);
})
    ->assert('id', '\d+')
    ->assert('bool', '(tak|nie)')
    ->convert('bool', function($v) { return strtolower($v) == 'tak' ? true : false; });

$app->run();
