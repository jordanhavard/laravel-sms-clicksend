<?php

namespace JordanHavard\ClickSend\Controllers;

use JordanHavard\ClickSend\Exceptions\APIException;

class BaseController
{
    protected function validateResponse($response)
    {
        $status = $response->http_code;

        switch ($status) {
            case 400:
                return throw APIException::badRequest();
            case 401:
                return throw APIException::unauthorised();
            case 403:
                return throw APIException::forbidden();
            case 404:
                return throw APIException::notFound();
            case 405:
                return throw APIException::badMethod();
            case 429:
                return throw APIException::rateLimited();
            case 500:
                return throw APIException::serverError();
            case $status < 200 || $status > 208:
                return throw APIException::badResponse($status);
        }

    }
}
