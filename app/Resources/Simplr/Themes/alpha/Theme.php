<?php
namespace Simplr\Themes\Alpha;

use Simplr\PluggableBundle\BaseTheme;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Theme extends BaseTheme
{
    public function getConfiguration()
    {
        return array(
            'description' => 'The example theme called \'Alpha\'',
        );
    }

    public function register(EventDispatcherInterface $dispatcher)
    {
        // $dispatcher->...
    }
}