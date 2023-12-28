<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\EventListener;

use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\SteppedForm\EventDispatcher\Event\FormFinished;

final class FormFinishedEventListener
{
    public function __construct(private readonly FormSubmitterInterface $submitter)
    {
    }

    public function handle(FormFinished $event): void
    {
        if ($this->submitter->supportsSubmitting($event->entity)) {
            $this->submitter->submit($event->entity);
        }
    }
}
