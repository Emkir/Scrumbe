<?php
namespace Scrumbe\Bundle\ProjectBundle\Services;

use Scrumbe\Models\ProjectQuery;
use BasePeer;

class ProjectService {

    public function getProjects()
    {
        $projectsArray = array();

        $projects = ProjectQuery::create()->find();

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
} 