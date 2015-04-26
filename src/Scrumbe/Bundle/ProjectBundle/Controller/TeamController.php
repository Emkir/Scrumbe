<?php
namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TeamController extends Controller
{
    public function teamAction()
    {
        return $this->render('ScrumbeProjectBundle:team:team.html.twig');
    }
}
