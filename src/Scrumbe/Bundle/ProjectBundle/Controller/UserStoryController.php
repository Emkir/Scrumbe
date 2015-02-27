<?php

namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Scrumbe\Models\UserStory;
use Scrumbe\Models\UserStoryQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserStoryController extends Controller
{
	/**
     * Get all user strories
     *
     * @param  Integer   $projectId     Project's id
     * @return Response                 Twig view with all uss in JSON
     */
    public function getUserStoriesAction($projectId)
    {
        $usService     = $this->container->get('userstory_service');
        $userStories   = $usService->getUserStories($projectId);

        return $this->render('ScrumbeProjectBundle:userstories:userStories.html.twig',
            array('user_stories' => $userStories)
        );
    }

    /**
     * Get single user story
     *
     * @param  Integer      $projectId      Project's id
     * @param  Integer      $userStoryId    User story's id
     * @return Response                     Twig view with all us in JSON
     */
    public function getUserStoryAction($projectId, $userStoryId)
    {
        $usService     = $this->container->get('userstory_service');
        $validatorService   = $this->container->get('scrumbe.validator_service');

        $validatorService->objectExists($userStoryId, UserStoryQuery::create());
        $userStory     = $usService->getUserStory($projectId, $userStoryId);

        return $this->render('ScrumbeProjectBundle:userstories:userStory.html.twig',
            array('user_story' => $userStory)
        );
    }

    /**
     * Create a user story
     *
     * @param  Integer             $projectId      Project's id
     * @return RedirectResponse                    Get to the created user story page
     *         Response                            Twig view with form
     */
    public function postUserStoryAction($projectId)
    {
        $usService = $this->container->get('userstory_service');
        $userStory = $usService->createUserStory($projectId);

        if ($userStory instanceof UserStory)
        {
            return $this->redirect($this->generateUrl('scrumbe_get_user_story',array('projectId' => $projectId, 'userStoryId' => $userStory->getId())));
        }

        return $this->render('ScrumbeProjectBundle:userstories:createUserStory.html.twig', array(
            'form' => $userStory->createView()
        ));    
    }

    /**
     * Update a user story
     *
     * @param  Integer                          $projectId      Project's id
     * @param  Integer          $userStoryId    User story's id
     * @return RedirectResponse                 Get to the updated user story page
     *         Response                         Twig view with form
     */
	public function putUserStoryAction($projectId, $userStoryId)
    {
        $usService = $this->container->get('userstory_service');
        $validatorService   = $this->container->get('scrumbe.validator_service');

        $validatorService->objectExists($userStoryId, UserStoryQuery::create());
        $userStory = $usService->updateUserStory($projectId, $userStoryId);

        if ($userStory instanceof UserStory)
        {
            return $this->redirect($this->generateUrl('scrumbe_get_user_story',array('projectId' => $projectId, 'userStoryId' => $userStory->getId())));
        }

        return $this->render('ScrumbeProjectBundle:userstories:createUserStory.html.twig', array(
            'form' => $userStory->createView(),
        ));    
    }

    /**
     * Delete a user story
     *
     * @param  Integer                          $projectId      Project's id
     * @param  Integer          $userStoryId    User story's id
     * @return RedirectResponse                 Get to the user story list of the project
     */
    public function deleteUserStoryAction($projectId, $userStoryId)
    {
        $usService = $this->container->get('userstory_service');
        $validatorService   = $this->container->get('scrumbe.validator_service');

        $validatorService->objectExists($userStoryId, UserStoryQuery::create());
        $usService->deleteUs($projectId, $userStoryId);
        
        return $this->redirect($this->generateUrl('scrumbe_get_user_stories',array('projectId' => $projectId)));
    }



}