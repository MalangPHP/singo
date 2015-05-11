<?php

namespace Singo\Bus\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class InvalidCommandException
 * @package Singo\Bus\Exception
 */
class InvalidCommandException extends HttpException
{
    public function __construct($message = null, \Exception $previous, $code = 0)
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message, $previous, [], $code);
    }
}
