<?php
namespace Scrumbe\Bundle\ProjectBundle\Services;

use Scrumbe\Models\KanbanTaskQuery;
use Scrumbe\Models\SprintQuery;
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

        $conn   = \Propel::getConnection();
        $sql    = '
                SELECT t.*, us.label as label, us.priority as priority, us.number as us_number, kt.task_position as position
                FROM task as t
                LEFT JOIN user_story as us ON us.id = t.user_story_id
                LEFT JOIN link_user_story_sprint as luss ON luss.user_story_id = us.id
                LEFT JOIN sprint as s ON s.id = luss.sprint_id
                LEFT JOIN kanban_task as kt ON kt.task_id = t.id
                WHERE us.project_id = :projectId
                AND CURDATE() >= DATE(s.start_date)
                AND CURDATE() <= DATE(s.end_date)
                ORDER BY kt.task_position ASC
            ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':projectId', $projectId, \PDO::PARAM_INT);
        $stmt->execute();
        $tasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach($tasks as $task)
            $tasksArray[$task['progress']][$task['position']] = $task;

        return $tasksArray;
    }

    public function getBacklogTasks($projectId)
    {
        $tasksArray = array();

        $tasks = TaskQuery::create()
            ->useUserStoryQuery()
                ->filterByProjectId($projectId)
                ->orderByNumber()
            ->endUse()
            ->find();

        foreach($tasks as $task)
        {
            $tasksArray[] = $task->toArray(BasePeer::TYPE_FIELDNAME);
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

    public function saveKanbanPosition($taskId, $taskPosition)
    {
        $task = TaskQuery::create()->findPk($taskId);

        $conn   = \Propel::getConnection();
        $sql    = '
                SELECT s.id
                FROM sprint as s
                WHERE s.project_id = :projectId
                AND CURDATE() >= DATE(s.start_date)
                AND CURDATE() <= DATE(s.end_date)
            ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':projectId', $task->getUserStory()->getProjectId(), \PDO::PARAM_INT);
        $stmt->execute();
        $currentSprint= $stmt->fetch(\PDO::FETCH_ASSOC);

        $sprintId = $currentSprint['id'];
        $kanbanTask = KanbanTaskQuery::create()->filterBySprintId($sprintId)->filterByTaskId($taskId)->findOne();

        $oldProgress = $task->getProgress();
        $oldPosition = $kanbanTask->getTaskPosition();
        $newProgress = $taskPosition['progress'];
        $newPosition = $taskPosition['position'];
        $kanbanTask->setTaskPosition($newPosition);
        $task->setProgress($newProgress);
        $task->save();
        $kanbanTask->save();

        $kanbanTaskId = $kanbanTask->getId();

        if ($oldProgress !== $newProgress)
        {
            $tasksInNewProgress = KanbanTaskQuery::create()
                ->useTaskQuery()
                ->filterByProgress($newProgress)
                ->endUse()
                ->filterBySprintId($sprintId)
                ->find();

            if (!$tasksInNewProgress->isEmpty())
            {
                foreach ($tasksInNewProgress as $taskInNewProgress)
                {
                    $position = $taskInNewProgress->getTaskPosition();
                    if ($position >= $newPosition && $taskInNewProgress->getId() != $kanbanTaskId)
                    {
                        $taskInNewProgress->setTaskPosition($position + 1);
                        $taskInNewProgress->save();
                    }
                }
            }

            $tasksInOldProgress = KanbanTaskQuery::create()
                ->useTaskQuery()
                ->filterByProgress($oldProgress)
                ->endUse()
                ->filterBySprintId($sprintId)
                ->find();

            if (!$tasksInOldProgress->isEmpty())
            {
                foreach ($tasksInOldProgress as $taskInOldProgress)
                {
                    $position = $taskInOldProgress->getTaskPosition();
                    if ($position > $oldPosition)
                    {
                        $taskInOldProgress->setTaskPosition($position - 1);
                        $taskInOldProgress->save();
                    }
                }
            }
        }
        else
        {
            $tasksInProgress = KanbanTaskQuery::create()
                ->useTaskQuery()
                ->filterByProgress($newProgress)
                ->endUse()
                ->filterBySprintId($sprintId)
                ->find();

            if (!$tasksInProgress->isEmpty())
            {
                foreach ($tasksInProgress as $taskInProgress)
                {
                    $position = $taskInProgress->getTaskPosition();
                    if ($oldPosition < $newPosition && $position > $oldPosition && $position <= $newPosition && $taskInProgress->getId() != $kanbanTaskId)
                    {
                        $taskInProgress->setTaskPosition($position - 1);
                        $taskInProgress->save();
                    }
                    elseif ($oldPosition > $newPosition && $position < $oldPosition && $position >= $newPosition && $taskInProgress->getId() != $kanbanTaskId)
                    {
                        $taskInProgress->setTaskPosition($position + 1);
                        $taskInProgress->save();
                    }
                }
            }
        }
    }

}