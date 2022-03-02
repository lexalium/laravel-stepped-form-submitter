<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\EventListener;

use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\SteppedForm\EventDispatcher\Event\FormFinished;

class FormFinishedEventListener
{
    public function __construct(
        private FormSubmitterInterface $submitter,
    ) {
    }

    public function handle(FormFinished $event): void
    {
        if ($this->submitter->supportsSubmitting($event->getEntity())) {
            $this->submitter->submit($event->getEntity());
        }
    }
}
