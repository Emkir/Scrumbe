<?php
namespace Scrumbe\Bundle\FrontOfficeBundle\Services;

use Scrumbe\Exception\ForbiddenException;
use Scrumbe\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ValidatorService {

    /**
     * Check if object exists by its id
     *
     * @param Integer       $objectId       Object's id toi check if exists
     * @param ObjectQuery   $queryClass     The query class of the object to check
     * @param String        $objectType     The object's type
     */
    public function objectExistsById($objectId, $queryClass, $objectType)
    {
        $object = $queryClass->findPk($objectId);
        if (is_null($object))
        {
            throw new NotFoundHttpException('object.not_found.' . $objectType, null, Response::HTTP_NOT_FOUND);

        }
    }

    public function objectExistsMultipleColumns($columns, $queryClass, $objectType)
    {
        $object = $queryClass;

        foreach ($columns as $column => $value)
        {
            $object = $object->filterBy($column, $value);
        }

        $object = $object->findOne();
        if (is_null($object))
        {
            throw new NotFoundException('object.not_found.' . $objectType);
        }
    }

    public function userAccessOnObject($objectId, $user, $queryClass, $objectType)
    {
        $objectHasAccess = $queryClass::create()->userHasAccess($objectId, $user);

        if (!$objectHasAccess)
        {
            throw new ForbiddenException('object.forbidden.' . $objectType);
        }
    }
} 