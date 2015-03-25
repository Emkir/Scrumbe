<?php
namespace Scrumbe\Bundle\UserBundle\Services;

use BasePeer;
use Scrumbe\Models\UserQuery;

class UserService {

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

} 