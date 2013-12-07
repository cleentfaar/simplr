<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\Services;

use Doctrine\ORM\EntityManager;

/**
 * Class OptionManager
 * @package Simplr\Services
 */
class OptionManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var null|array
     */
    private $options;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        if (!isset($this->options)) {
            $this->options = $this->fetchOptions();
        }
        return $this->options;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getOptionValue($name, $default = null)
    {
        $options = $this->getOptions();
        if (isset($options[$name])) {
            return $options[$name]->getValue();
        }
        return $default;
    }

    /**
     * @return array
     */
    private function fetchOptions()
    {
        $result = $this->em->getRepository('Simplr:Option')->findAll();
        $options = array();
        foreach ($result as $option) {
            $options[$option->getName()] = $option;
        }
        return $options;
    }
}
