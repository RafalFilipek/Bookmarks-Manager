<?php

require_once __DIR__.'/setup.php';

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
})->bind('index');

$app->get($app['translator']->trans('routing.set_marked'), function($id, $bool) use ($app) {
    $count = $app['db']->executeUpdate('UPDATE Category SET marked = ? WHERE ID = ?', array($bool, $id));
    $app['memcache']->delete('categories-list');
    return json_encode($count == 1);
})
    ->assert('id', '\d+')
    ->convert('bool', function($v) use($app) { return (bool)$v; })
    ->bind('mark_category');

$app->run();