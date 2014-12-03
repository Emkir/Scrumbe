<?php
namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectController extends Controller
{
    /**
     * Get all projects
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getProjectsAction()
    {
        $projectService = $this->get('project_service');
        $projects = $projectService->getProjects();

        return $this->render('ScrumbeProjectBundle:projects:projects.html.twig',
            array('projects' => $projects)
        );
    }

    /**
     * Get one project
     * @param Integer   $projectId      Project Id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getProjectAction($projectId)
    {
        $projectService = $this->get('project_service');
        $project = $projectService->getProject($projectId);

        return $this->render('ScrumbeProjectBundle:projects:project.html.twig',
            array('project' => $project)
        );
    }

}
