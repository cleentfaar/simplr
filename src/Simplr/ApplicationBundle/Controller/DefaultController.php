<?php

namespace Simplr\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SimplrApplicationBundle:Default:index.html.twig', array('name' => $name));
    }
}
