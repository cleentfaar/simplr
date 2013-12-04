<?php

/*
 * This file is part of the Simplr package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simplr\CmsBundle\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImagesController
{

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $templating;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $pageRepository;

    /**
     * @param EngineInterface $templating
     * @param ObjectRepository $repository
     */
    public function __construct(EngineInterface $templating, ObjectRepository $repository)
    {
        $this->templating = $templating;
        $this->pageRepository = $repository;
    }

    /**
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function indexAction($slug)
    {
        /**
         * - perform logic to retrieve a Media object
         * - resize the related image on the filesystem, creating it at a location accessible with the given $slug
         * - send a response with the output of the created image (this should all only happen once for each image)
         */
        $response = new Response($output, 200, $headers);
        return $response;
    }
}
