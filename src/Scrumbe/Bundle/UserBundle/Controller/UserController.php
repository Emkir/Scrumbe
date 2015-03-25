<?php

namespace Scrumbe\Bundle\UserBundle\Controller;

use Scrumbe\Bundle\UserBundle\Form\Type\UserType;
use Scrumbe\Models\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class UserController extends Controller
{
    public function postUserAction(Request $request)
    {
        $userService = $this->container->get('user.user_service');
        $user = new User();

        $form = $this->createForm(new UserType(), $user);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $user = $form->getData();
            $user->setUsername($user->getEmail());
            $user->setPassword(hash('sha512', $user->getPassword()));

            $emailExists = $userService->checkEmail($user->getEmail());

            if (!$emailExists)
                $user->save();

            return $this->redirect($this->generateUrl('index'));
        }

        return $this->redirect($this->generateUrl('index'));
    }
}
