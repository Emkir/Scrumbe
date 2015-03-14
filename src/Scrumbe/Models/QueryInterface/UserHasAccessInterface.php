<?php
namespace Scrumbe\Models\QueryInterface;

interface UserHasAccessInterface {

    public function userHasAccess($objectId, $user);
} 