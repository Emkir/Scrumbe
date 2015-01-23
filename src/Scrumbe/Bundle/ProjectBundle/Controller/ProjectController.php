<?php
namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Scrumbe\Models\Project;
use Scrumbe\Models\ProjectQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

    public function postProjectAction(Request $request)
    {
        $project = new Project();
        $form = $this->createForm('project', $project);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $project = $form->getData();
            $project->save();

            return $this->redirect($this->generateUrl('scrumbe_get_project', array('projectId' => $project->getId())));
        }

        return $this->render('ScrumbeProjectBundle:projects:createProject.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function putProjectAction($projectId, Request $request)
    {

    }

    public function deleteProjectAction($projectId)
    {

    }
}
