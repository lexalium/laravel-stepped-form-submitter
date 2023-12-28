<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\Tests;

use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\LaravelSteppedFormSubmitter\Exception\NoSubmittersAddedException;
use Lexal\LaravelSteppedFormSubmitter\Factory\FormSubmitterFactoryInterface;
use Lexal\LaravelSteppedFormSubmitter\FormSubmitter;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class FormSubmitterTest extends TestCase
{
    private FormSubmitterFactoryInterface&Stub $factory;

    private FormSubmitterInterface $submitter;

    protected function setUp(): void
    {
        $this->factory = $this->createStub(FormSubmitterFactoryInterface::class);

        $this->submitter = new FormSubmitter($this->factory, []);
    }

    /**
     * @throws NoSubmittersAddedException
     */
    public function testSubmitter(): void
    {
        $extendedSubmitter = $this->createMock(FormSubmitterInterface::class);

        $this->factory->method('create')
            ->willReturn($extendedSubmitter);

        $extendedSubmitter->expects($this->once())
            ->method('supportsSubmitting')
            ->with('test')
            ->willReturn(true);

        $extendedSubmitter->expects($this->once())
            ->method('submit')
            ->with('test')
            ->willReturn('result');

        $supports = $this->submitter->supportsSubmitting('test');
        $result = $this->submitter->submit('test');

        self::assertTrue($supports);
        self::assertEquals('result', $result);
    }
}
