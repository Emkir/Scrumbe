<?php
namespace Scrumbe\Bundle\ProjectBundle\Services;

use Scrumbe\Models\Task;
use Scrumbe\Models\TaskQuery;
use Scrumbe\Bundle\ProjectBundle\Form\Type\TaskType;
use BasePeer;

class TaskService {
	protected $form;
    protected $container;

    public function __construct($form,$container)
    {
        $this->form = $form;
        $this->container = $container;
    }

    public function getKanbanTasks($projectId)
    {
        $tasksArray = array();

        $tasks = TaskQuery::create()
            ->useUserStoryQuery()
                ->filterByProjectId($projectId)
            ->endUse()
            ->orderByPosition()
            ->find();

        foreach($tasks as $task)
        {
            $tasksArray[$task->getProgress()][$task->getPosition()] = $task->toArray(BasePeer::TYPE_FIELDNAME);
            $userStory = $task->getUserStory();
            $tasksArray[$task->getProgress()][$task->getPosition()]['label'] = $userStory->getLabel();
            $tasksArray[$task->getProgress()][$task->getPosition()]['priority'] = $userStory->getPriority();
        }

        return $tasksArray;
    }

    public function getTask($usId, $taskId)
    {
        $task = TaskQuery::create()->filterByUserStoryId($usId)->findPk($taskId);
        $taskArray = $task->toArray(BasePeer::TYPE_FIELDNAME);

        return $taskArray;
    }

    public function createTask($usId)
     {
        $task = new Task;
        $form = $this->form->create(new TaskType, $task);
        $request = $this->container->get('request');
        $form->get('user_story_id')->setData($usId);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $task = $form->getData();
            $task->save();

            return $task;
        }

        return $form;
    }

    public function updateTask($usId, $taskId)
    {
        $task = TaskQuery::create()->filterByUserStoryId($usId)->findPk($taskId);
    	$form = $this->form->create(new TaskType, $task);

        $request = $this->container->get('request');
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $task = $form->getData();
            $task->save();

            return $task;
        }

        return $form;
    }

    public function deleteTask($usId, $taskId)
    {
        $task = TaskQuery::create()->filterByUserStoryId($usId)->findPk($usId);
        $task->delete();

        return true;
    }


}