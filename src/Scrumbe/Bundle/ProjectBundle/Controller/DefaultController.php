<?php

namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ScrumbeProjectBundle:Default:index.html.twig', array('name' => $name));
    }
}
