<?php
namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class KanbanController extends Controller
{
    public function kanbanAction()
    {
        return $this->render('ScrumbeProjectBundle:kanban:kanban.html.twig');
    }
}
