<?php

namespace Scrumbe\FrontOfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('ScrumbeFrontOfficeBundle:Home:index.html.twig');
    }

}
