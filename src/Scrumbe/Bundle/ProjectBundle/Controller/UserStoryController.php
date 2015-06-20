<?php

namespace Scrumbe\Bundle\ProjectBundle\Controller;

use Scrumbe\Models\UserStory;
use Scrumbe\Models\UserStoryQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @ JsonResponse
     */
    public function postUserStoryAction(Request $request)
    {
        $data = $request->request->all();

        $priority = array("0"=>"low", "1"=>"medium", "2"=>"high");
        $lastUserStory = UserStoryQuery::create()
            ->filterByProjectId($data['project_id'])
            ->orderByNumber(\Criteria::DESC)
            ->limit(1)
            ->findOne();

        $userStory = new UserStory();
        $userStory->setProjectId($data['project_id']);
        $userStory->setDescription($data['description']);
        $userStory->setPriority($priority[$data['priority']]);
        $userStory->setNumber($lastUserStory->getNumber() + 1);
        $userStory->save();

        return new JsonResponse(array('user_story' => $userStory), JsonResponse::HTTP_CREATED);
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

    public function saveKanbanPositionAction(Request $request, $userStoryId)
    {
        $userStoryPosition = $request->request->all();
        $usService = $this->container->get('userstory_service');

        $validatorService = $this->container->get('scrumbe.validator_service');
        $validatorService->objectExistsById($userStoryId, UserStoryQuery::create(), 'user_story');
        $usService->saveKanbanPosition($userStoryId, $userStoryPosition);

        return new JsonResponse(array("code" => JsonResponse::HTTP_OK), JsonResponse::HTTP_OK);
    }

}
