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

    public function getUss($projectId)
    {
        $ussArray = array();

        $uss = UserStoryQuery::create()->filterByProjectId($projectId)->find();

        foreach($uss as $key=>$us)
        {
            $ussArray[$key] = $this->getUs($projectId,$us->getId());
        }

        return $ussArray;
    }

    public function getUs($projectId, $usId)
    {
        $us = UserStoryQuery::create()->filterByProjectId($projectId)->findPk($usId);
        $usArray = $us->toArray(BasePeer::TYPE_FIELDNAME);

        return $usArray;
    }

    public function createUs($projectId)
     {
        $us = new UserStory;
        $form = $this->form->create(new UserStoryType, $us);
        $request = $this->container->get('request');
        $form->get('project_id')->setData($projectId);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $us = $form->getData();
            $us->save();

            return $us;
        }

        return $form;
    }

    public function updateUs($projectId, $usId)
    {
        $us = UserStoryQuery::create()->filterByProjectId($projectId)->findPk($usId);
    	$form = $this->form->create(new UserStoryType, $us);

        $request = $this->container->get('request');
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $us = $form->getData();
            $us->save();

            return $us;
        }

        return $form;
    }

    public function deleteUs($projectId, $usId)
    {
        $us = UserStoryQuery::create()->filterByProjectId($projectId)->findPk($usId);
        $us->delete();

        return true;
    }


}