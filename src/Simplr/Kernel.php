<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr;

use Silex\Application;

use Herrera\Wise\WiseServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Simplr\Doctrine\Extensions\TablePrefix;
use Simplr\Services\MediaManager;
use Simplr\Services\OptionManager;
use Simplr\Services\PageManager;
use Simplr\Services\PluginManager;
use Simplr\Services\ThemeManager;
use Simplr\Twig\SimplrExtension;
use Simplr\Controller\Frontend\ImageResizeController;
use Simplr\Controller\Frontend\PageController;
use Simplr\Event\GetRoutesEvent;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;


/**
 * Class Kernel
 * @package Simplr
 */
class Kernel
{

    /**
     * @var \Silex\Application
     */
    private $app;

    /**
     * @var bool
     */
    private $registered = false;

    /**
     * @param string $env
     */
    public function __construct($env = 'prod', $autoRegister = false)
    {
        switch ($env) {
            case 'test':
            case 'dev':
                Debug::enable();
                break;
            default:
                break;
        }
        $this->env = $env;
        $this->app = new Application();
        $this->prepareConfiguration();
        if ($autoRegister == true) {
            $this->registerServices();
        }
    }

    public function run()
    {
        if ($this->registered !== true) {
            $this->registerServices();
        }
        $this->loadRoutes();
        $this->app->run();
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    private function loadRoutes()
    {
        $this->app['pages.controller'] = $this->app->share(function() {
            return new PageController($this->app);
        });
        $this->app['imageresize.controller'] = $this->app->share(function() {
            return new ImageResizeController($this->app);
        });
        $this->app->get('/assets/media/images/{path}', 'imageresize.controller:resizeAction');
        $this->app['routes'] = $this->app->extend('routes', function (RouteCollection $routes, Application $app) {
            $event = new GetRoutesEvent();
            $app['dispatcher']->dispatch(Events::GET_BACKEND_ROUTES, $event);
            $backendCollection = $event->getCollection();
            $backendCollection->addPrefix($app['config']['simplr']['backend_prefix']);
            $routes->addCollection($backendCollection);

            $event = new GetRoutesEvent();
            $app['dispatcher']->dispatch(Events::GET_FRONTEND_ROUTES, $event);
            $frontendCollection = $event->getCollection();
            $frontendCollection->addCollection($app['simplr_pagemanager']->getActivePageRoutes());
            $routes->addCollection($frontendCollection);

            return $routes;
        });
        /**
        $app->get('/', function () use ($app) {
        $activeTheme = $app['simplr_thememanager']->getActiveTheme();
        $activePlugins = $app['simplr_pluginmanager']->getActivePlugins();
        return $app['twig']->render('index.html.twig', array('activeTheme' => $activeTheme, 'activePlugins' => $activePlugins));
        })
        ->bind('homepage');
         */

        $this->app->error(function (\Exception $e, $code) {
            if ($this->app['debug']) {
                throw $e;
            }

            // 404.html, or 40x.html, or 4xx.html, or error.html
            $templates = array(
                'errors/'.$code.'.html.twig',
                'errors/'.substr($code, 0, 2).'x.html.twig',
                'errors/'.substr($code, 0, 1).'xx.html.twig',
                'errors/default.html.twig',
                '@simplr/errors/'.$code.'.html.twig',
                '@simplr/errors/'.substr($code, 0, 2).'x.html.twig',
                '@simplr/errors/'.substr($code, 0, 1).'xx.html.twig',
                '@simplr/errors/default.html.twig',
            );

            return new Response($this->app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
        });

    }

    private function prepareConfiguration()
    {
        $wiseOptions = array(
            'wise.path' => SIMPLR_PATHTO_CONFIG,
        );
        if ($this->env === 'prod') {
            $wiseOptions['wise.cache_dir'] = SIMPLR_PATHTO_CACHE . '/config';
        }
        $this->app->register(
            new WiseServiceProvider(),
            $wiseOptions
        );
        $parameters = $this->app['wise']->load('parameters.yml');
        $parameters = array_merge($parameters, array(
            'path_to_root' => SIMPLR_PATHTO_ROOT,
            'path_to_app' => SIMPLR_PATHTO_APP,
            'path_to_cache' => SIMPLR_PATHTO_CACHE,
            'path_to_config' => SIMPLR_PATHTO_CONFIG,
            'path_to_library' => SIMPLR_PATHTO_LIBRARY,
            'path_to_logs' => SIMPLR_PATHTO_LOGS,
        ));
        $this->app['wise']->setGlobalParameters($parameters);
        $this->app['parameters'] = $parameters;
        $this->app['services'] = $this->app['wise']->load('services.yml');
        $this->app['config'] = $this->app['wise']->load('config_' . $this->env . '.yml');
    }

    private function registerServices()
    {
        $this->registerDefaultServices();
        $this->registered = true;
    }

    private function registerDefaultServices()
    {
        $this->app->register(new UrlGeneratorServiceProvider());
        $this->app->register(new ValidatorServiceProvider());
        $this->app->register(new ServiceControllerServiceProvider());

        $this->app->register(new TwigServiceProvider(), array(
            //'debug' => $this->app['config']['simplr']['debug'],
            'twig.options' => array(
                'debug' => $this->app['config']['simplr']['debug'] ? true : false,
                //'cache' => $this->app['config']['simplr']['debug'] ? false : SIMPLR_PATHTO_CACHE . '/twig',
                'cache' => SIMPLR_PATHTO_CACHE . '/twig',
            ),
        ));

        $dbOptions = array(
            'driver' => $this->app['config']['simplr']['database']['driver'],
        );
        switch ($dbOptions['driver']) {
            case 'pdo_mysql':
                $dbOptions['dbname'] = $this->app['config']['simplr']['database']['name'];
                $dbOptions['username'] = $this->app['config']['simplr']['database']['username'];
                $dbOptions['password'] = $this->app['config']['simplr']['database']['password'];
                $dbOptions['host'] = $this->app['config']['simplr']['database']['host'];
                $dbOptions['port'] = $this->app['config']['simplr']['database']['port'];
                break;
            case 'pdo_sqlite':
                $dbOptions['path'] = $this->app['config']['simplr']['database']['path'];
                break;
            default:
                throw new \Exception(sprintf("Unknown driver for database: %s", $dbOptions['driver']));
        }
        $this->app->register(new DoctrineServiceProvider, array(
            "db.options" => $dbOptions
        ));

        $this->app->register(new DoctrineOrmServiceProvider, array(
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
        $tablePrefix = new TablePrefix($this->app['config']['simplr']['database']['table_prefix']);
        $this->app['db.event_manager']->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefix);
        $this->app['simplr_optionmanager'] = new OptionManager($this->app['orm.em']);
        $this->app['simplr_mediamanager'] = new MediaManager($this->app['orm.em']);
        $this->app['simplr_pagemanager'] = new PageManager($this->app['orm.em']);

        $this->app['simplr_pluginmanager'] = new PluginManager($this->app['simplr_optionmanager']);
        $this->app['simplr_pluginmanager']->registerListeners($this->app['dispatcher']);

        $this->app['simplr_thememanager'] = new ThemeManager($this->app['simplr_optionmanager']);
        $this->app['simplr_thememanager']->registerListeners($this->app['dispatcher']);

        $this->app['twig'] = $this->app->share($this->app->extend('twig', function($twig, $app) {
            $app['twig.loader.filesystem']->addPath(SIMPLR_PATHTO_LIBRARY . '/Resources/views', 'simplr');
            $activeTheme = $app['simplr_thememanager']->getActiveTheme();
            if ($activeTheme !== null) {
                $app['twig.loader.filesystem']->addPath(SIMPLR_PATHTO_THEMES . '/' . $activeTheme .'/templates');
            }
            $twig->addExtension(new SimplrExtension($app));
            return $twig;
        }));


        if ($this->app['config']['simplr']['debug'] == true) {
            $this->app['debug'] = true;


            $this->app->register(new MonologServiceProvider(), array(
                'monolog.logfile' => SIMPLR_PATHTO_LOGS . '/simplr.log',
            ));

            $this->app->register($p = new WebProfilerServiceProvider(), array(
                'profiler.cache_dir' => SIMPLR_PATHTO_CACHE . '/profiler',
            ));
            $this->app->mount('/_profiler', $p);
        }

    }
}