<?php
namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Scrumbe\Bundle\ProjectBundle\Form\Type\ProjectType;
use Scrumbe\Models\LinkProjectUser;
use Scrumbe\Models\Project;
use Scrumbe\Models\ProjectQuery;
use Scrumbe\Models\UserQuery;
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
        $projects           = $projectService->getProjects($this->getUser());
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
        $userStoryService   = $this->container->get('userstory_service');
        $taskService        = $this->container->get('task_service');

        $validatorService   = $this->container->get('scrumbe.validator_service');

        $validatorService->objectExistsMultipleColumns(
            array(
                'Id' => $projectId,
                'UrlName' => $projectName
            ),
            ProjectQuery::create(),
            'project'
        );

        $validatorService->userAccessOnObject($projectId, $this->getUser(), new ProjectQuery(), 'project');

        $project = $projectService->getProject($projectId);
        $project['user_stories'] = $userStoryService->getKanbanUserStories($projectId);
        $project['tasks'] = $taskService->getKanbanTasks($projectId);

        return $this->render('ScrumbeProjectBundle:projects:kanban.html.twig',
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
        $admin = new LinkProjectUser;

        $projectService = $this->container->get('project_service');
        $form = $this->createForm(new ProjectType, $project);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $project = $form->getData();

            //GET && SET cover
            $allData = $request->request->all();
            $projectData = $allData['project'];
            $cover = $projectData['cover_project'];
            if($cover !== '')
            {
                $project->setCoverProject($cover);
            }

            //Save Project  
            $sanitizeUrl = $projectService->sanitizeProjectNameToUrl($project->getName());
            $project->setUrlName($sanitizeUrl);          
            $project->save();

            //SET Admin
            $user = $this->getUser();
            $admin->setProjectId($project->getId());
            $admin->setUserId($user->getId());
            $admin->setAdmin($user->getId());
            $admin->save();

            return $this->redirect($this->generateUrl('scrumbe_get_project', array('projectId' => $project->getId(), 'projectName' => $project->getUrlName())));
        }

         return new JsonResponse(array('errors' => $form->getErrors()), Response::HTTP_CREATED);
    }

    /**
     * Update a project
     *
     * @param Request       $request        The PUT request
     * @param Integer       $projectId      The project's id
     * @return JSONsonResponse                 Response with updated project or errors
     */
    public function putProjectAction(Request $request, $projectId)
    {
        $validatorService   = $this->container->get('scrumbe.validator_service');
        $projectService = $this->container->get('project_service');
        $validatorService->objectExistsById($projectId, ProjectQuery::create(), 'project');

        $project = ProjectQuery::create()->findPk($projectId);
        $form = $this->createForm(new ProjectType, $project, array('method' => 'PUT'));

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $project = $form->getData();

            //GET && SET cover
            $allData = $request->request->all();
            $projectData = $allData['project'];
            $cover = $projectData['cover_project'];
            if($cover !== '')
            {
                $project->setCoverProject($cover);
            }

            //Save Project
            $sanitizeUrl = $projectService->sanitizeProjectNameToUrl($project->getName());
            $project->setUrlName($sanitizeUrl);
            $project->save();

            return $this->redirect($this->generateUrl('scrumbe_get_projects'));
        }

        return new JsonResponse(array('errors' => $form->getErrors()), Response::HTTP_BAD_REQUEST);
    }

    /** 
    * Add users to a project
    *
    * @param Array         $userName       The username's list
    * @return JSONsonResponse              Response with updated project or errors
    */
    public function addUsersToProjectAction(Request $request, $projectId)
    {
        $project = new Project;

        $username = $request->query->get('username');

        for ($i=0; $i < count($username); $i++) { 

            $link_project_user = new LinkProjectUser;
            
            $user_id = UserQuery::create()->filterByUsername($username[$i])->findOne()->getId();
            $link_project_user->setUserId($user_id)->setProjectId($projectId)->setAdmin(0);     
            $link_project_user->save();

        }
        die('stop');
      
        return $this->redirect($this->generateUrl('scrumbe_get_projects'));
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
        
        return $this->redirect($this->generateUrl('scrumbe_get_projects'));
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

        $template = $this->render('ScrumbeProjectBundle:projects:projectForm.html.twig', array(
            'projectCreateForm' => $projectForm->createView()
        ))->getContent();

        $json = json_encode($template);
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Display team page
     *
     */
    public function teamAction()
    {
        return $this->render('ScrumbeProjectBundle:projects:team.html.twig');
    }

    /**
     * Display sprint page
     *
     */
    public function sprintAction()
    {
        return $this->render('ScrumbeProjectBundle:projects:sprint.html.twig');
    }
}
