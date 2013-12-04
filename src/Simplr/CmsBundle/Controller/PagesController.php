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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PagesController
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
        $slug = '/' . ltrim($slug, '/');
        $page = $this->pageRepository->findOneBy(array('slug' => $slug));
        if ($page === null) {
            throw new NotFoundHttpException(sprintf("Could not find page with slug '%s'", $slug));
        }
        return $this->templating->renderResponse(
            '@current_theme/index.html.twig',
            array('page' => $page)
        );
    }
}
