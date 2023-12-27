<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\Factory;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Lexal\FormSubmitter\FormSubmitter;
use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\FormSubmitter\Transaction\TransactionInterface;
use Lexal\FormSubmitter\TransactionalFormSubmitter;
use Lexal\LaravelSteppedFormSubmitter\Exception\NoSubmittersAddedException;

use function array_map;
use function is_string;

final class FormSubmitterFactory implements FormSubmitterFactoryInterface
{
    public function __construct(private readonly Container $container)
    {
    }

    /**
     * @inheritDoc
     *
     * @throws BindingResolutionException
     */
    public function create(array $submitters): FormSubmitterInterface
    {
        if (!$submitters) {
            throw new NoSubmittersAddedException();
        }

        $formSubmitter = new FormSubmitter(
            ...array_map(fn (mixed $submitter): FormSubmitterInterface => $this->getInstance($submitter), $submitters),
        );

        if ($this->container->bound(TransactionInterface::class)) {
            $formSubmitter = new TransactionalFormSubmitter(
                $formSubmitter,
                $this->container->make(TransactionInterface::class),
            );
        }

        return $formSubmitter;
    }

    /**
     * @throws BindingResolutionException
     */
    private function getInstance(mixed $instance): mixed
    {
        return is_string($instance) ? $this->container->make($instance) : $instance;
    }
}
