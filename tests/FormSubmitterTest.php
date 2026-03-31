<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\Tests;

use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\LaravelSteppedFormSubmitter\Exception\NoSubmittersAddedException;
use Lexal\LaravelSteppedFormSubmitter\Factory\FormSubmitterFactoryInterface;
use Lexal\LaravelSteppedFormSubmitter\FormSubmitter;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use stdClass;

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
        $entity = new stdClass();

        $extendedSubmitter = $this->createMock(FormSubmitterInterface::class);

        $this->factory->method('create')
            ->willReturn($extendedSubmitter);

        $extendedSubmitter->expects($this->once())
            ->method('supportsSubmitting')
            ->with($entity)
            ->willReturn(true);

        $extendedSubmitter->expects($this->once())
            ->method('submit')
            ->with($entity)
            ->willReturn($entity);

        $supports = $this->submitter->supportsSubmitting($entity);
        $result = $this->submitter->submit($entity);

        self::assertTrue($supports);
        self::assertEquals($entity, $result);
    }
}
