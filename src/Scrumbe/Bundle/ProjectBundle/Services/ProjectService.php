<?php
namespace Scrumbe\Bundle\ProjectBundle\Services;

use Scrumbe\Models\Project;
use Scrumbe\Models\ProjectQuery;
use Scrumbe\Bundle\ProjectBundle\Form\Type\ProjectType;
use BasePeer;

class ProjectService {

    public function getProjects($user)
    {
        $projectsArray = array();

        $projects = ProjectQuery::create()
            ->useLinkProjectUserQuery()
                ->filterByUserId($user->getId())
            ->endUse()
            ->find();

        foreach($projects as $key=>$project)
        {
            $projectsArray[$key] = $this->getProject($project->getId());
        }

        return $projectsArray;
    }

    public function getProject($projectId)
    {
        $project = ProjectQuery::create()->findPk($projectId);
        $projectArray = $project->toArray(BasePeer::TYPE_FIELDNAME);

        return $projectArray;
    }

    public function deleteProject($projectId)
    {
        $project = ProjectQuery::create()->findPk($projectId);
        $project->delete();
    }

    public function uploadCover($file)
    {

    }
} 