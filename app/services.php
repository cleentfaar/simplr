<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app;

use Simplr\Application;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Herrera\Wise\WiseServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Simplr\Doctrine\Extensions\TablePrefix;
use Simplr\Services\MediaManager;
use Simplr\Services\OptionManager;
use Simplr\Services\PageManager;
use Simplr\Services\PluginManager;
use Simplr\Services\ThemeManager;
use Simplr\Twig\SimplrExtension;
use Symfony\Component\Debug\Debug;


$app = new Application();
$app['env'] = 'dev';
switch ($app['env']) {
    case 'test':
    case 'dev':
        Debug::enable();
        break;
    default:
        break;
}

$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());

/**
 * Config START
 */
$wiseOptions = array(
    'wise.path' => SIMPLR_PATHTO_CONFIG,
);
if ($app['env'] === 'prod') {
    $wiseOptions['wise.cache_dir'] = SIMPLR_PATHTO_CACHE . '/config';
}
$app->register(
    new WiseServiceProvider(),
    $wiseOptions
);
$parameters = $app['wise']->load('parameters.yml');
$parameters = array_merge($parameters, array(
    'path_to_root' => SIMPLR_PATHTO_ROOT,
    'path_to_app' => SIMPLR_PATHTO_APP,
    'path_to_cache' => SIMPLR_PATHTO_CACHE,
    'path_to_config' => SIMPLR_PATHTO_CONFIG,
    'path_to_library' => SIMPLR_PATHTO_LIBRARY,
    'path_to_logs' => SIMPLR_PATHTO_LOGS,
));
$app['wise']->setGlobalParameters($parameters);
$app['parameters'] = $parameters;
$app['services'] = $app['wise']->load('services.yml');
$app['config'] = $app['wise']->load('config_' . $app['env'] . '.yml');
$app['debug'] = $app['config']['simplr']['debug'] ? true : false;
/**
 * Config END
 */

/**
 * Database START
 */
$dbOptions = array(
    'driver' => $app['config']['simplr']['database']['driver'],
);
switch ($dbOptions['driver']) {
    case 'pdo_mysql':
        $dbOptions['dbname'] = $app['config']['simplr']['database']['name'];
        $dbOptions['username'] = $app['config']['simplr']['database']['username'];
        $dbOptions['password'] = $app['config']['simplr']['database']['password'];
        $dbOptions['host'] = $app['config']['simplr']['database']['host'];
        $dbOptions['port'] = $app['config']['simplr']['database']['port'];
        break;
    case 'pdo_sqlite':
        $dbOptions['path'] = $app['config']['simplr']['database']['path'];
        break;
    default:
        throw new \Exception(sprintf("Unknown driver for database: %s", $dbOptions['driver']));
}
$app->register(new DoctrineServiceProvider, array(
    "db.options" => $dbOptions
));
$app->register(new DoctrineOrmServiceProvider, array(
    "orm.proxies_dir" => SIMPLR_PATHTO_CACHE . "/proxies",
    "orm.em.options" => array(
        "mappings" => array(
            // Using actual filesystem paths
            array(
                "use_simple_annotation_reader" => false,
                "type" => "annotation",
                "namespace" => "Simplr\\Entity",
                "path" => SIMPLR_PATHTO_LIBRARY . "/Entity",
            ),
        ),
    ),
));
$tablePrefix = new TablePrefix($app['config']['simplr']['database']['table_prefix']);
$app['db.event_manager']->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefix);
/**
 * Database END
 */

/**
 * Simplr services START
 */
$app['simplr_optionmanager'] = new OptionManager($app['orm.em']);
$app['simplr_mediamanager'] = new MediaManager($app['orm.em']);
$app['simplr_pagemanager'] = new PageManager($app['orm.em']);

$app['simplr_pluginmanager'] = new PluginManager($app['simplr_optionmanager']);
$app['simplr_pluginmanager']->registerListeners($app['dispatcher']);

$app['simplr_thememanager'] = new ThemeManager($app['simplr_optionmanager']);
$app['simplr_thememanager']->registerListeners($app['dispatcher']);
/**
 * Simplr services END
 */

/**
 * Twig START
 */
$app->register(new TwigServiceProvider(), array(
    //'debug' => $app['config']['simplr']['debug'],
    'twig.options' => array(
        'debug' => $app['config']['simplr']['debug'] ? true : false,
        'cache' => SIMPLR_PATHTO_CACHE . '/twig',
    ),
));
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $app['twig.loader.filesystem']->addPath(SIMPLR_PATHTO_LIBRARY . '/Resources/views', 'simplr');
    $activeTheme = $app['simplr_thememanager']->getActiveTheme();
    if ($activeTheme !== null) {
        $app['twig.loader.filesystem']->addPath(SIMPLR_PATHTO_THEMES . '/' . $activeTheme .'/templates');
    }
    $twig->addExtension(new SimplrExtension($app));
    return $twig;
}));
/**
 * Twig END
 */

/**
 * Debug START
 */
if ($app['config']['simplr']['debug'] == true) {
    $app->register(new MonologServiceProvider(), array(
        'monolog.logfile' => SIMPLR_PATHTO_LOGS . '/simplr.log',
    ));

    $app->register($p = new WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => SIMPLR_PATHTO_CACHE . '/profiler',
    ));
    $app->mount('/_profiler', $p);
}
/**
 * Debug END
 */

/**
 * Return the Application object for front controllers to use
 */
return $app;