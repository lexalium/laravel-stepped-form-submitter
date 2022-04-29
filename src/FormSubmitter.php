<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter;

use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\LaravelSteppedFormSubmitter\Exception\NoSubmittersAddedException;
use Lexal\LaravelSteppedFormSubmitter\Factory\FormSubmitterFactoryInterface;

class FormSubmitter implements FormSubmitterInterface
{
    private ?FormSubmitterInterface $submitter = null;

    public function __construct(
        private FormSubmitterFactoryInterface $factory,
        /**
         * @var string[]|FormSubmitterInterface[]
         */
        private array $submitters,
        private bool $useTransactional = false,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws NoSubmittersAddedException
     */
    public function supportsSubmitting(mixed $entity): bool
    {
        return $this->getSubmitter()->supportsSubmitting($entity);
    }

    /**
     * @inheritDoc
     *
     * @throws NoSubmittersAddedException
     */
    public function submit(mixed $entity): mixed
    {
        return $this->getSubmitter()->submit($entity);
    }

    /**
     * @throws NoSubmittersAddedException
     */
    private function getSubmitter(): FormSubmitterInterface
    {
        if ($this->submitter === null) {
            $this->submitter = $this->factory->create($this->submitters, $this->useTransactional);
        }

        return $this->submitter;
    }
}
