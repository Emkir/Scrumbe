<?php
namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Scrumbe\Models\Project;
use Scrumbe\Models\ProjectQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectController extends Controller
{
    /**
     * Get all projects
     *
     * @return \Symfony\Component\HttpFoundation\Response       Twig view with all projects in JSON
     */
    public function getProjectsAction()
    {
        $projectService     = $this->container->get('project_service');
        $projects           = $projectService->getProjects();

        return $this->render('ScrumbeProjectBundle:projects:projects.html.twig',
            array('projects' => $projects)
        );
    }

    /**
     * Get one project
     *
     * @param   Integer   $projectId      Project Id
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function getProjectAction($projectId)
    {
        $projectService     = $this->container->get('project_service');
        $validatorService   = $this->container->get('scrumbe.validator_service');

        $validatorService->objectExists($projectId, ProjectQuery::create());

        $project = $projectService->getProject($projectId);

        return $this->render('ScrumbeProjectBundle:projects:project.html.twig',
            array('project' => $project)
        );
    }

    public function postProjectAction()
    {
        $projectService = $this->container->get('project_service');
        $project = $projectService->createProject();
        
        if ($project instanceof Project)
        {
            return $this->redirect($this->generateUrl('scrumbe_get_project', array('projectId' => $project->getId())));
        }

        return $this->render('ScrumbeProjectBundle:projects:createProject.html.twig', array(
            'form' => $project->createView(),
        ));    
    }

    public function putProjectAction($projectId)
    {
        $projectService = $this->container->get('project_service');
        $project = $projectService->updateProject($projectId);

        if ($project instanceof Project)
        {
            return $this->redirect($this->generateUrl('scrumbe_get_project', array('projectId' => $project->getId())));
        }

        return $this->render('ScrumbeProjectBundle:projects:createProject.html.twig', array(
            'form' => $project->createView(),
        ));    
    }

    public function deleteProjectAction($projectId)
    {
        $projectService = $this->container->get('project_service');
        $project = $projectService->deleteProject($projectId);
        
        return $this->redirect($this->generateUrl('scrumbe_get_projects'));
    }
}
