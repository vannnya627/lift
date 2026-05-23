<?php

namespace App\Domain\Exception;

class UserAlreadyExistsException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('User already exists');
    }
}
