<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\Exception;

use Exception;

final class NoSubmittersAddedException extends Exception
{
    public function __construct()
    {
        parent::__construct('You must register at least one submitter to be able to submit form.');
    }
}
