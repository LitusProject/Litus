<?php

namespace MailBundle\Component\Api\SibApi;

use Exception;

class SibApiHelperResponse
{
    public bool $success;
    public Exception $exception;

    public function __construct(bool $success, Exception $exception = null) {
        $this->success = $success;
        $this->exception = $exception;
    }

    public static function successful() {
        return new SibApiHelperResponse(true);
    }

    public static function unsuccessful(Exception $e) {
        return new SibApiHelperResponse(false, $e);
    }
}