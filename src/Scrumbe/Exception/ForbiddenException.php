<?php

namespace Scrumbe\Exception;

class ForbiddenException extends BaseException
{
    /**
     * @param string $message
     * @param int $httpCode
     */
    public function __construct($message, $httpCode = 403)
    {
        $this->message = $message;
        $this->setCode($httpCode);
    }
}