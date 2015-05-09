<?php

namespace Scrumbe\Bundle\UserBundle\Controller;

use Scrumbe\Bundle\UserBundle\Form\Type\UserType;
use Scrumbe\Models\User;
use Scrumbe\Models\UserQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class UserController extends Controller
{
    public function postUserAction(Request $request)
    {
        $userService = $this->container->get('user.user_service');
        $user = new User();
        $errorsArray = array();
        $requestData = $request->request->all();

        $form = $this->createForm(new UserType(), $user);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $emailExists = $userService->checkEmail($requestData['user']['email']);

            if ($emailExists)
            {
                $errorsArray['email'] = "user.create.errors.email.exists";
                $this->get('session')->getFlashBag()->set(
                    'postUserErrors',
                    $errorsArray
                );
                $this->get('session')->getFlashBag()->set(
                    'postUserValues',
                    $request->request->get('user')
                );
                return $this->redirect($this->generateUrl('index'));
            }
            else
            {
                $user = $form->getData();
                $user->setUsername($user->getEmail());
                $user->setPassword(hash('sha512', $user->getPassword()));
                $user->setValidate(false);
                $token = bin2hex(openssl_random_pseudo_bytes(16));
                $user->setValidationToken($token);

                $user->save();
                $userService->sendConfirmEmail($user);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'user.create.success'
                );

                return $this->redirect($this->generateUrl('index'));
            }
        }

        $errorsArray = $this->getErrorMessages($form);

        $this->get('session')->getFlashBag()->set(
            'postUserErrors',
            $errorsArray
        );
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

    private function getErrorMessages(Form $form) {
        $errors = array();

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        foreach ($form->getErrors() as $key => $error) {
            $errors[$key] = $error->getMessage();
        }


        return $errors;
    }
}
