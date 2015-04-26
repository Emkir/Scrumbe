<?php
namespace Scrumbe\Bundle\UserBundle\Services;

use BasePeer;
use Scrumbe\Models\UserQuery;

class UserService {

    protected $translator;
    protected $mailService;

    public function __construct($translator, $mailService)
    {
        $this->translator = $translator;
        $this->mailService = $mailService;
    }

    public function getUserByUsername($username)
    {
        $userArray = array();

        $user = UserQuery::create()->findOneByUsername($username);

        if (!is_null($user))
            $userArray = $user->toArray(BasePeer::TYPE_FIELDNAME);

        return $userArray;
    }

    public function checkEmail($email)
    {
        $user = UserQuery::create()->findOneByEmail($email);

        if (is_null($user))
            return false;

        return true;
    }

    public function sendConfirmEmail($user)
    {
        $subject = $this->translator->trans("user.mail.confirm.subject");

        $this->mailService->sendConfirmEmail($subject, $user->toArray(BasePeer::TYPE_FIELDNAME), $user->getEmail(), "ScrumbeUserBundle:Emails:signup_confirm.html.twig");
    }

} 