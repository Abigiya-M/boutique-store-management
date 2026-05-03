<?php

/**
 * HTTP Exception
 * For HTTP errors (404, 403, 500, etc.)
 */

namespace App\Exceptions;

class HttpException extends \Exception
{
    public function __construct($code = 500, $message = "")
    {
        if (empty($message)) {
            $messages = [
                400 => 'Bad Request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                500 => 'Internal Server Error',
                503 => 'Service Unavailable',
            ];
            $message = $messages[$code] ?? "Error $code";
        }
        parent::__construct($message, $code);
    }
}
