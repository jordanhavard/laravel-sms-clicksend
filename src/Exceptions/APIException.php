<?php

namespace JordanHavard\ClickSend\Exceptions;

use Exception;

class APIException extends Exception
{
    public static function badRequest(): static
    {
        return new static('BAD_REQUEST',400);
    }

    public static function unauthorised(): static
    {
        return new static('UNAUTHORISED',401);
    }

    public static function forbidden(): static
    {
        return new static('FORBIDDEN',403);
    }

    public static function notFound(): static
    {
        return new static('NOT_FOUND',404);
    }

    public static function badMethod(): static
    {
        return new static('BAD_METHOD',405);
    }

    public static function rateLimited(): static
    {
        return new static('TOO_MANY_REQUESTS',405);
    }

    public static function serverError(): static
    {
        return new static('INTERNAL_SERVER_ERROR',405);
    }

    public static function badResponse($status): static
    {
        return new static('RESPONSE_NOT_OK',$status);
    }
}
