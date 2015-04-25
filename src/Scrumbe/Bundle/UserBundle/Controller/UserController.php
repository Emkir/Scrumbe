<?php

namespace Scrumbe\Bundle\UserBundle\Controller;

use Scrumbe\Bundle\UserBundle\Form\Type\UserType;
use Scrumbe\Models\User;
use Scrumbe\Models\UserQuery;
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
            $user->setValidate(false);
            $token = bin2hex(openssl_random_pseudo_bytes(16));
            $user->setValidationToken($token);

            $emailExists = $userService->checkEmail($user->getEmail());

            if (!$emailExists)
                $user->save();

            $userService->sendConfirmEmail($user);

            return $this->redirect($this->generateUrl('index'));
        }

        return $this->redirect($this->generateUrl('index'));
    }

    public function validateUserAction(Request $request, $userId)
    {
        $token = $request->query->get('token');

        $user = UserQuery::create()->findPk($userId);

        if($user->getValidationToken() == $token)
        {
            $user->setValidate(true);
            $user->save();
        }
        return $this->redirect($this->generateUrl('index'));
    }
}
