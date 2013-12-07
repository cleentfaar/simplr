<?php
namespace Cleentfaar\Simplr\Bundle\CmsBundle\EventListener;

use Cleentfaar\Simplr\Core\Controller\BaseController;
use Cleentfaar\Simplr\Core\Services\PluginManager;
use Cleentfaar\Simplr\Core\Services\ThemeManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class ControllerListener
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \Cleentfaar\Simplr\Core\Services\ThemeManager
     */
    private $themeManager;

    /**
     * @var \Cleentfaar\Simplr\Core\Services\PluginManager
     */
    private $pluginManager;

    public function __construct(EventDispatcherInterface $dispatcher, ThemeManager $themeManager, PluginManager $pluginManager)
    {
        $this->dispatcher = $dispatcher;
        $this->pluginManager = $pluginManager;
        $this->themeManager = $themeManager;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
        * $controller passed can be either a class or a Closure. This is not usual in Symfony2 but it may happen.
        * If it is a class, it comes in array format
        */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof BaseController) {
            $this->themeManager->registerActiveTheme($this->dispatcher);
            $this->pluginManager->registerActivePlugins($this->dispatcher);
        }
    }
}