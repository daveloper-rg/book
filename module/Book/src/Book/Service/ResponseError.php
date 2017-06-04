<?php
namespace Book\Service;

use Exception;

class ServiceException extends Exception { }

class ResponseError extends ServiceException {

    public function __construct($uri) {
        parent::__construct("Request for {$uri} did not respond properly.");
    }

}