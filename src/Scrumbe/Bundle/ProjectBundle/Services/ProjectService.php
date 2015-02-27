<?php
namespace Scrumbe\Bundle\ProjectBundle\Services;

use Scrumbe\Models\Project;
use Scrumbe\Models\ProjectQuery;
use Scrumbe\Bundle\ProjectBundle\Form\Type\ProjectType;
use BasePeer;

class ProjectService {

    protected $form;
    protected $container;

    public function __construct($form,$container)
    {
        $this->form = $form;
        $this->container = $container;
    }

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

    public function createProject()
    {
        $project = new Project;
        $form = $this->form->create(new ProjectType, $project);

        $request = $this->container->get('request');
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $project = $form->getData();
            $project->save();

            return $project;
        }

        return $form;
    }

    public function deleteProject($projectId)
    {
        $project = ProjectQuery::create()->findPk($projectId);
        $project->delete();

        return true;
    }

    public function updateProject($projectId)
    {
        $project = ProjectQuery::create()->findPk($projectId);
        $form = $this->form->create(new ProjectType, $project);

        $request = $this->container->get('request');
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $project = $form->getData();
            $project->save();

            return $project;
        }

        return $form;
    }
} 