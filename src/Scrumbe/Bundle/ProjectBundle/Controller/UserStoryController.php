<?php

namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Scrumbe\Models\UserStory;
use Scrumbe\Models\UserStoryQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserStoryController extends Controller
{
	/**
     * Get all us
     *
     * @return \Symfony\Component\HttpFoundation\Response       Twig view with all uss in JSON
     */
    public function getUssAction($projectId)
    {
        $usService     = $this->container->get('userstory_service');
        $us            = $usService->getUss($projectId);

        return $this->render('ScrumbeProjectBundle:userstories:userstories.html.twig',
            array('uss' => $us)
        );
    }

    /**
     * Get single us
     *
     * @return \Symfony\Component\HttpFoundation\Response       Twig view with all us in JSON
     */
    public function getUsAction($projectId, $usId)
    {
        $usService     = $this->container->get('userstory_service');
        $us           = $usService->getUs($projectId, $usId);

        return $this->render('ScrumbeProjectBundle:userstories:userstories.html.twig',
            array('uss' => $us)
        );
    }


    public function postUsAction($projectId)
    {
        $usService = $this->container->get('userstory_service');
        $us = $usService->createUs($projectId);

        if ($us instanceof UserStory)
        {
            return $this->redirect($this->generateUrl('scrumbe_get_us',array('projectId' => $projectId, 'usId' => $us->getId())));
        }

        return $this->render('ScrumbeProjectBundle:userstories:createUS.html.twig', array(
            'form' => $us->createView()
        ));    
    }

	public function putUsAction($projectId, $usId)
    {
        $usService = $this->container->get('userstory_service');
        $us = $usService->updateUs($projectId,$usId);

        if ($us instanceof UserStory)
        {
            return $this->redirect($this->generateUrl('scrumbe_get_us',array('projectId' => $projectId, 'usId' => $us->getId())));
        }

        return $this->render('ScrumbeProjectBundle:userstories:createUS.html.twig', array(
            'form' => $us->createView(),
        ));    
    }

    public function deleteUsAction($projectId, $usId)
    {
        $usService = $this->container->get('userstory_service');
        $us = $usService->deleteUs($projectId,$usId);
        
        return $this->redirect($this->generateUrl('scrumbe_get_uss',array('projectId' => $projectId)));
    }



}
