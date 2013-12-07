<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cleentfaar\Simplr\Core\Controller;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller
{

    /**
     * @param string|\Doctrine\ORM\EntityRepository|object $entity
     * @param int $id
     * @return null|object
     */
    protected function findOr404($entity, $id)
    {
        return $this->findByOr404($entity, array('id' => $id), true);
    }

    /**
     * Lookup an entity or throw a HttpNotFound exception
     *
     * (inspired by [private bundle, sorry!])
     *
     * @param string|\Doctrine\ORM\EntityRepository|object $entity
     * @param array $criterias
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return null|object|array
     */
    protected function findByOr404($entity, array $criterias = array(), $singleResult = false)
    {
        $result = null;

        if (is_string($entity)) {
            /**
             * @var EntityRepository $repository
             */
            $repository = $this->getDoctrine()->getManager()->getRepository($entity);
        } else {
            throw new \InvalidArgumentException("Must supply a classname (string)");
        }
        if ($singleResult === true) {
            $result = $repository->findOneBy($criterias);
        } else {
            $result = $repository->findBy($criterias);
        }

        if (null !== $result) {
            return $result;
        }

        throw $this->createNotFoundException('Resource not found');
    }
}
