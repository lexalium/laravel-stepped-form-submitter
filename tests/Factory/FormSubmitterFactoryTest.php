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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

final class FormSubmitterFactoryTest extends TestCase
{
    private Container&Stub $container;

    private FormSubmitterFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->container = $this->createStub(Container::class);

        $this->factory = new FormSubmitterFactory($this->container);
    }

    /**
     * @throws BindingResolutionException
     * @throws NoSubmittersAddedException
     */
    public function testCreate(): void
    {
        $submitter = $this->createMock(FormSubmitterInterface::class);

        $this->container->method('bound')
            ->willReturn(false);

        $this->container->method('make')
            ->willReturn($submitter);

        $formSubmitter = $this->factory->create(['submitter_class']);

        self::assertEquals(new FormSubmitter($submitter), $formSubmitter);
    }

    /**
     * @throws NoSubmittersAddedException
     * @throws BindingResolutionException
     */
    public function testCreateWithTransaction(): void
    {
        $transaction = $this->createMock(TransactionInterface::class);
        $submitter = $this->createMock(FormSubmitterInterface::class);

        $this->container->method('bound')
            ->willReturn(true);

        $this->container->method('make')
            ->willReturn($transaction);

        $expected = new TransactionalFormSubmitter(new FormSubmitter($submitter), $transaction);

        $formSubmitter = $this->factory->create([$submitter]);

        self::assertEquals($expected, $formSubmitter);
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
}
