<?php
namespace Scrumbe\Bundle\FrontOfficeBundle\Services;

use Scrumbe\Models\ProjectQuery;
use BasePeer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ValidatorService {

    public function objectExists($objectId, $queryClass)
    {
        $object = $queryClass->findPk($objectId);
        if ($object === null)
            throw new NotFoundHttpException('La ressource recherchée n\'existe pas', null, Response::HTTP_NOT_FOUND);
    }
} 