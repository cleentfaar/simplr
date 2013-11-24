<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\Command;

use Knp\Command\Command;
use Doctrine\ORM\Tools\EntityGenerator;

/**
 * Base class for Doctrine console commands to extend from.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class DoctrineCommand extends Command
{
    /**
     * get a doctrine entity generator
     *
     * @return EntityGenerator
     */
    protected function getEntityGenerator()
    {
        $entityGenerator = new EntityGenerator();
        $entityGenerator->setGenerateAnnotations(false);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

        return $entityGenerator;
    }

    /**
     * Get a doctrine entity manager by symfony name.
     *
     * @param string $name
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager($name)
    {
        return $this->getSilexApplication()['orm.em'];
    }

    /**
     * Get a doctrine dbal connection by symfony name.
     *
     * @param string $name
     * @return \Doctrine\DBAL\Connection
     */
    protected function getDoctrineConnection($name)
    {
        return $this->getSilexApplication()['orm.em']->getConnection($name);
    }
}
