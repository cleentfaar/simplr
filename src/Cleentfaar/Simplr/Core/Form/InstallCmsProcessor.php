<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;

class InstallCmsProcessor
{
    /**
     * @var array
     */
    private $data = array(
        'site_title' => null,
        'site_url' => null,
        'database_driver' => null,
    );

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $failedReasons = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function getFailedReasons()
    {
        return $this->failedReasons;
    }

    public function hasInstallationFailed()
    {
        return empty($this->failedReasons) ? false : true;
    }

    public function install()
    {
        $this->failedReasons = array();
        return true;
    }
}