<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\Tests\ServiceProvider;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\FormSubmitter\Transaction\TransactionInterface;
use Lexal\LaravelSteppedFormSubmitter\EventListener\FormFinishedEventListener;
use Lexal\LaravelSteppedFormSubmitter\Factory\FormSubmitterFactoryInterface;
use Lexal\LaravelSteppedFormSubmitter\ServiceProvider\ServiceProvider;
use Lexal\LaravelSteppedFormSubmitter\Tests\TestApplication;
use Lexal\LaravelSteppedFormSubmitter\Tests\TestDispatcher;
use Lexal\SteppedForm\EventDispatcher\Event\FormFinished;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function dirname;
use function sprintf;

final class ServiceProviderTest extends TestCase
{
    public function testBoot(): void
    {
        $app = new TestApplication(
            defaultConfig: ['form-submitter' => ['transaction_class' => 'custom']],
        );

        $app->singleton(Dispatcher::class, static fn () => new TestDispatcher());

        $serviceProvider = new ServiceProvider($app);

        $serviceProvider->boot();

        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'form-submitter.php';

        self::assertEquals([$path => 'form-submitter.php'], ServiceProvider::pathsToPublish(ServiceProvider::class));

        /** @var Repository $config */
        $config = $app->get('config');

        $expectedConfig = [
            'transaction_class' => 'custom',
            'submitters' => [],
        ];

        self::assertEquals($expectedConfig, $config->get('form-submitter'));

        /** @var TestDispatcher $dispatcher */
        $dispatcher = $app->get(Dispatcher::class);

        self::assertEquals([FormFinishedEventListener::class, 'handle'], $dispatcher->getListener(FormFinished::class));
    }

    public function testBootWithoutDispatcher(): void
    {
        $app = new TestApplication(
            defaultConfig: ['form-submitter' => ['transaction_class' => 'custom', 'submitters' => []]],
        );

        $serviceProvider = new ServiceProvider($app);

        $serviceProvider->boot();

        $app->singleton(Dispatcher::class, static fn () => new TestDispatcher());
        /** @var TestDispatcher $dispatcher */
        $dispatcher = $app->get(Dispatcher::class);

        self::assertNull($dispatcher->getListener(FormFinished::class));
    }

    public function testRegister(): void
    {
        $app = new TestApplication(
            defaultConfig: ['form-submitter' => [
                'transaction_class' => null,
                'submitters' => [
                    self::createFormSubmitter(),
                    'form_submitter',
                ],
            ]],
        );

        $app->singleton('form_submitter', static fn () => self::createFormSubmitter());
        $app->singleton(Container::class, static fn () => $app);

        $serviceProvider = new ServiceProvider($app);

        $serviceProvider->boot();
        $serviceProvider->register();

        self::assertTrue($app->bound(FormSubmitterFactoryInterface::class));
        self::assertTrue($app->isShared(FormSubmitterFactoryInterface::class));

        $app->get(FormSubmitterFactoryInterface::class);

        self::assertTrue($app->bound(FormSubmitterInterface::class));
        self::assertTrue($app->isShared(FormSubmitterInterface::class));

        $app->get(FormSubmitterInterface::class);
    }

    #[DataProvider('registerWithTransactionDataProvider')]
    public function testRegisterWithTransaction(TransactionInterface|string $transactionClass): void
    {
        $app = new TestApplication(
            defaultConfig: ['form-submitter' => [
                'transaction_class' => $transactionClass,
                'submitters' => [
                    self::createFormSubmitter(),
                ],
            ]],
        );

        $app->singleton('transaction', static fn () => self::createTransaction());

        $serviceProvider = new ServiceProvider($app);

        $serviceProvider->boot();
        $serviceProvider->register();

        self::assertTrue($app->bound(TransactionInterface::class));
        self::assertTrue($app->isShared(TransactionInterface::class));

        $app->get(TransactionInterface::class);
    }

    /**
     * @return iterable<string, array{ 0: TransactionInterface|string }>
     */
    public static function registerWithTransactionDataProvider(): iterable
    {
        yield 'concrete instance' => [self::createTransaction()];

        yield 'service alias' => ['transaction'];
    }

    public function testRegisterThrowsExceptionWhenConfigRepositoryIsNotRegistered(): void
    {
        $this->expectExceptionObject(
            new LogicException(
                sprintf(
                    'Unable to register classes without binding of %s contract to the concrete class.',
                    Repository::class,
                ),
            ),
        );

        $serviceProvider = new ServiceProvider(
            new TestApplication(
                boundCallback: static function (string $abstract) {
                    return $abstract !== Repository::class && $abstract !== Dispatcher::class;
                },
            ),
        );

        $serviceProvider->boot();
        $serviceProvider->register();
    }

    private static function createFormSubmitter(): FormSubmitterInterface
    {
        return new class () implements FormSubmitterInterface {
            public function supportsSubmitting(mixed $entity): bool
            {
                return true;
            }

            public function submit(mixed $entity): mixed
            {
                return $entity;
            }
        };
    }

    private static function createTransaction(): TransactionInterface
    {
        return new class () implements TransactionInterface {
            public function start(): void
            {
                // nothing to do
            }

            public function commit(): void
            {
                // nothing to do
            }

            public function rollback(): void
            {
                // nothing to do
            }
        };
    }
}
