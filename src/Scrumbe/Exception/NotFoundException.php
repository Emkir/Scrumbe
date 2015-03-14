<?php

namespace Scrumbe\Exception;

class NotFoundException extends BaseException
{
    /**
     * @param string $message
     * @param int $httpCode
     */
    public function __construct($message, $httpCode = 404)
    {
        $this->message = $message;
        $this->setCode($httpCode);
    }
}