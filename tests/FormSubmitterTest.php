<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\Tests;

use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\LaravelSteppedFormSubmitter\Exception\NoSubmittersAddedException;
use Lexal\LaravelSteppedFormSubmitter\Factory\FormSubmitterFactoryInterface;
use Lexal\LaravelSteppedFormSubmitter\FormSubmitter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FormSubmitterTest extends TestCase
{
    private MockObject $factory;

    private FormSubmitterInterface $submitter;

    /**
     * @throws NoSubmittersAddedException
     */
    public function testSubmitter(): void
    {
        $extendedSubmitter = $this->createMock(FormSubmitterInterface::class);

        $this->factory->expects($this->once())
            ->method('create')
            ->with([], false)
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

        $this->assertEquals(true, $supports);
        $this->assertEquals('result', $result);
    }

    protected function setUp(): void
    {
        $this->factory = $this->createMock(FormSubmitterFactoryInterface::class);

        $this->submitter = new FormSubmitter($this->factory, []);

        parent::setUp();
    }
}
