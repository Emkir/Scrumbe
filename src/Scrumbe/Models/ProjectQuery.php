<?php

namespace Scrumbe\Models;

use Scrumbe\Models\om\BaseProjectQuery;
use Scrumbe\Models\QueryInterface\UserHasAccessInterface;

class ProjectQuery extends BaseProjectQuery implements UserHasAccessInterface
{
    public function userHasAccess($objectId, $user)
    {
        $project = $this->useLinkProjectUserQuery()
                            ->filterByUserId($user->getId())
                            ->filterByProjectId($objectId)
                        ->endUse()
                        ->findOne();

        if (!is_null($project))
        {
            return true;
        }
        return false;
    }
}
