<?php
namespace Scrumbe\Bundle\ProjectBundle\Services;

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

    public function getUserStories($projectId)
    {
        $userStoriesArray = array();

        $userStories = UserStoryQuery::create()->filterByProjectId($projectId)->orderByPosition()->find();

        foreach($userStories as $userStory)
        {
            $userStoriesArray[$userStory->getPosition()] = $userStory->toArray(BasePeer::TYPE_FIELDNAME);
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
        $userStory = UserStoryQuery::create()->findPk($userStoryId);
        $userStory->setPosition($newPosition['position']);
        $userStory->save();

        $userStoriesInSprint = UserStoryQuery::create()->filterByProjectId($userStory->getProjectId())->find();
        if (!$userStoriesInSprint->isEmpty())
        {
            foreach ($userStoriesInSprint as $userStoryInSprint)
            {
                $position = $userStoryInSprint->getPosition();
                if ($userStoryInSprint->getPosition() >= $newPosition['position'] && $userStoryInSprint->getId() != $userStoryId)
                {
                    $userStoryInSprint->setPosition($position + 1);
                    $userStoryInSprint->save();
                }
            }
        }
    }


}