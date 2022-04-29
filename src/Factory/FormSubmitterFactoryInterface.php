<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\Factory;

use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\LaravelSteppedFormSubmitter\Exception\NoSubmittersAddedException;

interface FormSubmitterFactoryInterface
{
    /**
     * @param string[]|FormSubmitterInterface[] $submitters
     *
     * @throws NoSubmittersAddedException
     */
    public function create(array $submitters, bool $useTransactional = false): FormSubmitterInterface;
}
