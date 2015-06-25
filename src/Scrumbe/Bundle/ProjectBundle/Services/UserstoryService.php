<?php
namespace Scrumbe\Bundle\ProjectBundle\Services;

use Scrumbe\Models\LinkUserStorySprint;
use Scrumbe\Models\LinkUserStorySprintQuery;
use Scrumbe\Models\UserStory;
use Scrumbe\Models\UserStoryQuery;
use Scrumbe\Bundle\ProjectBundle\Form\Type\UserStoryType;
use BasePeer;

class UserstoryService {
	protected $form;
    protected $container;

    public function __construct($form,$container)
    {
        $this->form = $form;
        $this->container = $container;
    }

    public function getKanbanUserStories($projectId)
    {
        $conn   = \Propel::getConnection();
        $sql    = '
                SELECT us.*, COUNT(t.id) as task_count, luss.user_story_position as position
                FROM user_story as us
                LEFT JOIN task as t ON t.user_story_id = us.id
                LEFT JOIN link_user_story_sprint as luss ON luss.user_story_id = us.id
                LEFT JOIN sprint as s ON s.id = luss.sprint_id
                WHERE us.project_id = :projectId
                AND CURDATE() >= DATE(s.start_date)
                AND CURDATE() <= DATE(s.end_date)
                GROUP BY us.id
                ORDER BY luss.user_story_position ASC
            ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':projectId', $projectId, \PDO::PARAM_INT);
        $stmt->execute();
        $userStories = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $userStories;
    }

    public function getBacklogUserStories($projectId)
    {
        $userStoriesArray = array();

        $userStories = UserStoryQuery::create()->filterByProjectId($projectId)->orderByNumber()->find();

        foreach($userStories as $userStory)
        {
            $userStoriesArray[$userStory->getId()] = $userStory->toArray(BasePeer::TYPE_FIELDNAME);
            $userStoriesArray[$userStory->getId()]['task_count'] = count($userStory->getTasks());
        }

        return $userStoriesArray;
    }

    public function getUserStory($projectId, $userStoryId)
    {
        $userStory      = UserStoryQuery::create()->filterByProjectId($projectId)->findPk($userStoryId);
        $userStoryArray = $userStory->toArray(BasePeer::TYPE_FIELDNAME);

        return $userStoryArray;
    }

    public function createUserStory($projectId)
    {
        $userStory  = new UserStory;
        $form       = $this->form->create(new UserStoryType, $userStory);
        $request    = $this->container->get('request');

        $form->get('project_id')->setData($projectId);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $userStory = $form->getData();
            $userStory->save();

            return $userStory;
        }

        return $form;
    }

    public function updateUserStory($projectId, $userStoryId)
    {
        $userStory  = UserStoryQuery::create()->filterByProjectId($projectId)->findPk($userStoryId);
    	$form       = $this->form->create(new UserStoryType, $userStory);

        $request = $this->container->get('request');
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $userStory = $form->getData();
            $userStory->save();

            return $userStory;
        }

        return $form;
    }

    public function deleteUserStory($projectId, $userStoryId)
    {
        $userStory = UserStoryQuery::create()->filterByProjectId($projectId)->findPk($userStoryId);
        $userStory->delete();

        return true;
    }

    public function saveKanbanPosition($userStoryId, $newPosition)
    {
        $conn   = \Propel::getConnection();
        $sql    = '
                SELECT luss.*
                FROM link_user_story_sprint as luss
                LEFT JOIN sprint as s ON s.id = luss.sprint_id
                WHERE luss.user_story_id = :userStoryId
                AND CURDATE() >= DATE(s.start_date)
                AND CURDATE() <= DATE(s.end_date)
            ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':userStoryId', $userStoryId, \PDO::PARAM_INT);
        $stmt->execute();
        $linkUserStorySprint = $stmt->fetch(\PDO::FETCH_ASSOC);

        $linkUserStorySprintObj = LinkUserStorySprintQuery::create()->findPk($linkUserStorySprint['id']);
        $oldPosition = $linkUserStorySprint['user_story_position'];
        $newPosition = $newPosition['position'];

        $linkUserStorySprintObj->setUserStoryPosition($newPosition);
        $linkUserStorySprintObj->save();

        $sql    = '
                SELECT us.id as user_story_id, luss.user_story_position as position, luss.id as link_id
                FROM user_story as us
                LEFT JOIN link_user_story_sprint as luss ON luss.user_story_id = us.id
                LEFT JOIN sprint as s ON s.id = luss.sprint_id
                WHERE luss.sprint_id = :sprintId
                AND CURDATE() >= DATE(s.start_date)
                AND CURDATE() <= DATE(s.end_date)
            ';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':sprintId', $linkUserStorySprint['sprint_id'], \PDO::PARAM_INT);
        $stmt->execute();
        $userStoriesInSprint = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($userStoriesInSprint))
        {
            foreach ($userStoriesInSprint as $userStoryInSprintArr)
            {
                $linkSprint = LinkUserStorySprintQuery::create()->findPk($userStoryInSprintArr['link_id']);
                $position = $userStoryInSprintArr['position'];
                if ($oldPosition < $newPosition && $position > $oldPosition && $position <= $newPosition && $userStoryInSprintArr['user_story_id'] != $userStoryId)
                {
                    $linkSprint->setUserStoryPosition($position - 1);
                    $linkSprint->save();
                }
                elseif ($oldPosition > $newPosition && $position < $oldPosition && $position >= $newPosition && $userStoryInSprintArr['user_story_id'] != $userStoryId)
                {
                    $linkSprint->setUserStoryPosition($position + 1);
                    $linkSprint->save();
                }
            }
        }
    }

}