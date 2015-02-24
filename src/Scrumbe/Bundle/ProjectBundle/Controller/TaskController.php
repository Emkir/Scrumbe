<?php

namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Scrumbe\Models\Task;
use Scrumbe\Models\TaskQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TaskController extends Controller
{
	/**
     * Get all task
     *
     * @return \Symfony\Component\HttpFoundation\Response       Twig view with all tasks in JSON
     */
    public function getTasksAction($projectId, $usId)
    {
        $taskService     = $this->container->get('task_service');
        $task            = $taskService->getTasks($usId);

        return $this->render('ScrumbeProjectBundle:tasks:tasks.html.twig',
            array('tasks' => $task)
        );
    }

    /**
     * Get single task
     *
     * @return \Symfony\Component\HttpFoundation\Response       Twig view with all task in JSON
     */
    public function getTaskAction($projectId, $usId, $taskId)
    {
        $validatorService   = $this->container->get('scrumbe.validator_service');
        $validatorService->objectExists($taskId, TaskQuery::create());
        $taskService    = $this->container->get('task_service');
        $task           = $taskService->getTask($usId, $taskId);

        return $this->render('ScrumbeProjectBundle:tasks:tasks.html.twig',
            array('tasks' => $task)
        );
    }


    public function postTaskAction($projectId, $usId)
    {
        $taskService = $this->container->get('task_service');
        $task = $taskService->createtask($usId);

        if ($task instanceof task)
        {
            return $this->redirect($this->generateUrl('scrumbe_get_task',array('projectId' => $projectId, 'usId' => $usId, 'taskId' => $task->getId())));
        }

        return $this->render('ScrumbeProjectBundle:tasks:createTask.html.twig', array(
            'form' => $task->createView()
        ));    
    }

	public function putTaskAction($projectId, $usId, $taskId)
    {
        $validatorService   = $this->container->get('scrumbe.validator_service');
        $validatorService->objectExists($taskId, TaskQuery::create());
        $taskService = $this->container->get('task_service');
        $task = $taskService->updatetask( $usId, $taskId);

        if ($task instanceof task)
        {
            return $this->redirect($this->generateUrl('scrumbe_get_task',array('projectId' => $projectId, 'usId' => $usId, 'taskId' => $task->getId())));
        }

        return $this->render('ScrumbeProjectBundle:tasks:createTask.html.twig', array(
            'form' => $task->createView(),
        ));    
    }

    public function deleteTaskAction($projectId, $usId, $taskId)
    {
        $validatorService   = $this->container->get('scrumbe.validator_service');
        $validatorService->objectExists($taskId, TaskQuery::create());
        $taskService = $this->container->get('task_service');
        $task = $taskService->deletetask($usId, $taskId);
        
        return $this->redirect($this->generateUrl('scrumbe_get_tasks',array('projectId' => $projectId, 'usId' => $usId)));
    }



}
