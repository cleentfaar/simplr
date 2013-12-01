<?php
namespace Simplr\Themes\Alpha;

use Simplr\PluggableBundle\BaseTheme;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Theme extends BaseTheme
{
    /**
     * @return array
     */
    public function getConfiguration()
    {
        return array(
            'description' => 'The example theme called \'Alpha\'',
        );
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function register(EventDispatcherInterface $dispatcher)
    {
        // $dispatcher->...
    }
}