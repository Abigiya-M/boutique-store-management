<?php

/**
 * Not Found Exception
 * Thrown when a resource is not found
 */

namespace App\Exceptions;

class NotFoundException extends HttpException
{
    public function __construct($message = "Resource not found")
    {
        parent::__construct(404, $message);
    }
}
