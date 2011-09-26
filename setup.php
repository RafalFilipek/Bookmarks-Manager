<?php

require_once __DIR__.'/silex.phar';

$app = new Silex\Application();

$app['autoloader']->registerNamespaces(array(
    'Symfony'   => __DIR__.'/vendor/symfony/src',
    'Rafal'     => __DIR__.'/vendor/rafal/src'
));

$app['debug'] = true;

$app->register(new Rafal\MemcacheServiceProvider\MemcacheServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'       => __DIR__.'/views',
    'twig.class_path' => __DIR__.'/vendor/twig/lib',
    'twig.options'    => array(
        'caches' => __DIR__.'/cache/twig'
    )
));

$app->register(new Rafal\ProfilerServiceProvider\ProfilerServiceProvider(), array(
    'profiler.data_url' => '__fetch_profiler_data',
    'profiler.cookie_name' => 'web_profiler'
));

$app->register(new Rafal\JavascriptRoutingServiceProvider\JavascriptRoutingServiceProvider(), array(
    'jsrouting.path'        => __DIR__.'/public/js',
    'jsrouting.file_name'   => 'r.js',
    'jsrouting.refresh'     => $app['debug']
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options'    => array(
        'dbname'    => 'bm',
        'host'      => '127.0.0.1',
        'user'      => 'root',
        'password'  => '',
        'driver'    => 'pdo_mysql'
    ),
    'db.dbal.class_path'    => __DIR__.'/vendor/doctrine-dbal/lib',
    'db.common.class_path'  => __DIR__.'/vendor/doctrine-common/lib'
));

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale'                    => 'pl',
    'locale_fallback'           => 'pl',
    'translation.class_path'    => __DIR__.'/vendor/symfony/src',
    'translator.messages'       => array(
        'pl' => __DIR__.'/locales/pl.yml',
        'en' => __DIR__.'/locales/en.yml'
    )
));

$app['translator.loader'] = new Symfony\Component\Translation\Loader\YamlFileLoader();