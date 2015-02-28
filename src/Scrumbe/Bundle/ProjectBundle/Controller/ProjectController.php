<?php
namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Scrumbe\Bundle\ProjectBundle\Form\Type\ProjectType;
use Scrumbe\Models\Project;
use Scrumbe\Models\ProjectQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    /**
     * Get all projects
     *
     * @return  Response    Twig view with all projects in JSON
     */
    public function getProjectsAction()
    {
        $projectService     = $this->container->get('project_service');
        $projects           = $projectService->getProjects();
        $projectForm        = $this->createForm(new ProjectType(), null, array(
            'action' => $this->generateUrl('scrumbe_post_project')
        ));

        return $this->render('ScrumbeProjectBundle:projects:projects.html.twig',
            array(
                'projects' => $projects,
                'projectCreateForm' => $projectForm->createView()
            )
        );
    }

    /**
     * Get one project
     *
     * @param   Integer   $projectId      Project's Id
     * @param   Integer   $projectName    Project's name
     * @return  Response                  Twig view with the project
     */
    public function getProjectAction($projectId, $projectName)
    {
        $projectService     = $this->container->get('project_service');
        $validatorService   = $this->container->get('scrumbe.validator_service');

        $validatorService->objectExistsMultipleColumns(
            array(
                'Id' => $projectId,
                'Name' => $projectName
            ),
            ProjectQuery::create(),
            'project'
        );

        $project = $projectService->getProject($projectId);

        return $this->render('ScrumbeProjectBundle:projects:project.html.twig',
            array('project' => $project)
        );
    }

    /**
     * Create a project
     *
     * @param Request       $request        The POST request
     * @return JsonResponse                 Response with project newly created or errors
     */
    public function postProjectAction(Request $request)
    {
        $project = new Project;
        $form = $this->createForm(new ProjectType, $project);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $project = $form->getData();
            $project->save();

            return new JsonResponse(array('project' => $project), Response::HTTP_CREATED);
        }

        return new JsonResponse(array('errors' => $form->getErrors()), Response::HTTP_CREATED);
    }

    /**
     * Update a project
     *
     * @param Request       $request        The PUT request
     * @param Integer       $projectId      The project's id
     * @return JsonResponse                 Response with updated project or errors
     */
    public function putProjectAction(Request $request, $projectId)
    {
        $validatorService   = $this->container->get('scrumbe.validator_service');
        $validatorService->objectExistsById($projectId, ProjectQuery::create(), 'project');

        $project = ProjectQuery::create()->findPk($projectId);
        $form = $this->createForm(new ProjectType, $project);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $project = $form->getData();
            $project->save();

            return new JsonResponse(array('project' => $project), Response::HTTP_OK);
        }

        return new JsonResponse(array('errors' => $form->getErrors()), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Delete a project
     *
     * @param Integer           $projectId      The project's id
     * @return JsonResponse                     Response with message of success
     */
    public function deleteProjectAction($projectId)
    {
        $projectService     = $this->container->get('project_service');
        $validatorService   = $this->container->get('scrumbe.validator_service');

        $validatorService->objectExistsById($projectId, ProjectQuery::create(), 'project');
        $projectService->deleteProject($projectId);
        
        return new JsonResponse(array('message' => 'project.delete.success'), Response::HTTP_OK);
    }

    /**
     * Return the form for creation or update
     *
     * @param   Integer | Null    $projectId        The project's id
     * @return  JsonResponse                        The form to display
     */
    public function getFormProjectAction($projectId)
    {
        if ($projectId)
        {
            $validatorService   = $this->container->get('scrumbe.validator_service');
            $validatorService->objectExistsById($projectId, ProjectQuery::create(), 'project');
            $project        = ProjectQuery::create()->findPk($projectId);
            $route          = 'scrumbe_put_project';
            $routeOptions   = array('projectId' => $projectId);
            $method         = 'PUT';
        }
        else
        {
            $project        = new Project();
            $route          = 'scrumbe_post_project';
            $method         = 'POST';
            $routeOptions   = array();
        }

        $projectForm  = $this->createForm(new ProjectType(), $project, array(
            'action' => $this->generateUrl($route, $routeOptions),
            'method' => $method
        ));
        var_dump($projectForm->getChildren());die();
        $test = $projectForm->createView();


        return new JsonResponse(array('form' => $projectForm->createView()));
    }
}
