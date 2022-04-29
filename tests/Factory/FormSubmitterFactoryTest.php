<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\Tests\Factory;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Lexal\FormSubmitter\FormSubmitter;
use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\FormSubmitter\Transaction\TransactionInterface;
use Lexal\FormSubmitter\TransactionalFormSubmitter;
use Lexal\LaravelSteppedFormSubmitter\Exception\NoSubmittersAddedException;
use Lexal\LaravelSteppedFormSubmitter\Factory\FormSubmitterFactory;
use Lexal\LaravelSteppedFormSubmitter\Factory\FormSubmitterFactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FormSubmitterFactoryTest extends TestCase
{
    private MockObject $container;

    private FormSubmitterFactoryInterface $factory;

    /**
     * @throws BindingResolutionException
     * @throws NoSubmittersAddedException
     */
    public function testCreate(): void
    {
        $submitter = $this->createMock(FormSubmitterInterface::class);

        $this->container->expects($this->once())
            ->method('make')
            ->with('submitter_class')
            ->willReturn($submitter);

        $formSubmitter = $this->factory->create(['submitter_class']);

        $this->assertEquals(new FormSubmitter($submitter), $formSubmitter);
    }

    /**
     * @throws NoSubmittersAddedException
     * @throws BindingResolutionException
     */
    public function testCreateWithTransaction(): void
    {
        $transaction = $this->createMock(TransactionInterface::class);
        $submitter = $this->createMock(FormSubmitterInterface::class);

        $this->container->expects($this->once())
            ->method('make')
            ->with(TransactionInterface::class)
            ->willReturn($transaction);

        $expected = new TransactionalFormSubmitter(new FormSubmitter($submitter), $transaction);

        $formSubmitter = $this->factory->create([$submitter], true);

        $this->assertEquals($expected, $formSubmitter);
    }

    /**
     * @throws BindingResolutionException
     * @throws NoSubmittersAddedException
     */
    public function testCreateNoSubmittersAddedException(): void
    {
        $this->expectExceptionObject(new NoSubmittersAddedException());

        $this->factory->create([]);
    }

    protected function setUp(): void
    {
        $this->container = $this->createMock(Container::class);

        $this->factory = new FormSubmitterFactory($this->container);

        parent::setUp();
    }
}
