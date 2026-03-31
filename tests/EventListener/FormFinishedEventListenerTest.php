<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\Tests\EventListener;

use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\LaravelSteppedFormSubmitter\EventListener\FormFinishedEventListener;
use Lexal\SteppedForm\EventDispatcher\Event\FormFinished;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

final class FormFinishedEventListenerTest extends TestCase
{
    private MockObject $formSubmitter;

    private FormFinishedEventListener $listener;

    protected function setUp(): void
    {
        $this->formSubmitter = $this->createMock(FormSubmitterInterface::class);

        $this->listener = new FormFinishedEventListener($this->formSubmitter);
    }

    public function testHandle(): void
    {
        $entity = new stdClass();

        $this->formSubmitter->method('supportsSubmitting')
            ->with($entity)
            ->willReturn(true);

        $this->formSubmitter->expects($this->once())
            ->method('submit')
            ->with($entity)
            ->willReturn('test');

        $this->listener->handle(new FormFinished($entity));
    }

    public function testHandleDoesNotSupport(): void
    {
        $entity = new stdClass();

        $this->formSubmitter->method('supportsSubmitting')
            ->willReturn(false);

        $this->formSubmitter->expects($this->never())
            ->method('submit');

        $this->listener->handle(new FormFinished($entity));
    }
}
