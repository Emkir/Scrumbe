<?php

namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Scrumbe\Models\KanbanTask;
use Scrumbe\Models\KanbanTaskQuery;
use Scrumbe\Models\LinkUserStorySprintQuery;
use Scrumbe\Models\Task;
use Scrumbe\Models\TaskQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends Controller
{
	/**
     * Get all task
     *
     * @return \Symfony\Component\HttpFoundation\Response       Twig view with all tasks in JSON
     */
    public function getTasksAction($projectId, $userStoryId)
    {
        $taskService     = $this->container->get('task_service');
        $task            = $taskService->getTasks($userStoryId);

        return $this->render('ScrumbeProjectBundle:tasks:tasks.html.twig',
            array('tasks' => $task)
        );
    }

    /**
     * Get single task
     *
     * @return \Symfony\Component\HttpFoundation\Response       Twig view with all task in JSON
     */
    public function getTaskAction($projectId, $userStoryId, $taskId)
    {
        $validatorService   = $this->container->get('scrumbe.validator_service');
        $validatorService->objectExists($taskId, TaskQuery::create());
        $taskService    = $this->container->get('task_service');
        $task           = $taskService->getTask($userStoryId, $taskId);

        return $this->render('ScrumbeProjectBundle:tasks:tasks.html.twig',
            array('tasks' => $task)
        );
    }


    public function postTaskAction(Request $request)
    {
        $data = $request->request->all();

        $task = new Task();
        $task->setUserStoryId($data['user_story_id']);
        $task->setDescription($data['description']);
        $task->setProgress('todo');
        $task->save();

        $sprints = LinkUserStorySprintQuery::create()->filterByUserStoryId($data['user_story_id'])->find();
        if (!$sprints->isEmpty())
        {
            foreach ($sprints as $sprint)
            {
                $kanbanTask = new KanbanTask();
                $kanbanTask->setSprintId($sprint->getId());
                $kanbanTask->setTaskId($task->getId());

                $lastTodo = KanbanTaskQuery::create()->filterBySprintId($sprint->getId())->useTaskQuery()->filterByProgress('todo')->endUse()->orderByTaskPosition('desc')->findOne();
                if ($lastTodo === null)
                    $kanbanTask->setTaskPosition(1);
                else
                    $kanbanTask->setTaskPosition($lastTodo->getTaskPosition() + 1);

                $kanbanTask->save();
            }
        }

        return new JsonResponse(array('task' => $task), JsonResponse::HTTP_CREATED);
    }

	public function putTaskAction($projectId, $userStoryId, $taskId)
    {
        $validatorService   = $this->container->get('scrumbe.validator_service');
        $validatorService->objectExists($taskId, TaskQuery::create());
        $taskService = $this->container->get('task_service');
        $task = $taskService->updatetask( $userStoryId, $taskId);

        if ($task instanceof task)
        {
            return $this->redirect($this->generateUrl('scrumbe_get_task',array('projectId' => $projectId, 'usId' => $userStoryId, 'taskId' => $task->getId())));
        }

        return $this->render('ScrumbeProjectBundle:tasks:createTask.html.twig', array(
            'form' => $task->createView(),
        ));    
    }

    public function deleteTaskAction($projectId, $userStoryId, $taskId)
    {
        $validatorService   = $this->container->get('scrumbe.validator_service');
        $validatorService->objectExists($taskId, TaskQuery::create());
        $taskService = $this->container->get('task_service');
        $task = $taskService->deletetask($userStoryId, $taskId);
        
        return $this->redirect($this->generateUrl('scrumbe_get_tasks',array('projectId' => $projectId, 'usId' => $userStoryId)));
    }

    public function saveKanbanPositionAction(Request $request, $taskId)
    {
        $taskPosition = $request->request->all();
        $taskService = $this->container->get('task_service');

        $validatorService = $this->container->get('scrumbe.validator_service');
        $validatorService->objectExistsById($taskId, TaskQuery::create(), 'task');
        $taskService->saveKanbanPosition($taskId, $taskPosition);

        return new JsonResponse(array("code" => JsonResponse::HTTP_OK), JsonResponse::HTTP_OK);
    }

}
