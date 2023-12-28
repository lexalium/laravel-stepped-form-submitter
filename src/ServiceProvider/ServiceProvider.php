<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\ServiceProvider;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\FormSubmitter\Transaction\TransactionInterface;
use Lexal\LaravelSteppedFormSubmitter\EventListener\FormFinishedEventListener;
use Lexal\LaravelSteppedFormSubmitter\Factory\FormSubmitterFactory;
use Lexal\LaravelSteppedFormSubmitter\Factory\FormSubmitterFactoryInterface;
use Lexal\LaravelSteppedFormSubmitter\FormSubmitter;
use Lexal\SteppedForm\EventDispatcher\Event\FormFinished;
use LogicException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function dirname;
use function sprintf;

final class ServiceProvider extends LaravelServiceProvider
{
    private const CONFIG_FILENAME = 'form-submitter.php';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function boot(): void
    {
        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME;

        $this->publishes([
            $path => $this->app->configPath(self::CONFIG_FILENAME),
        ]);

        $this->mergeConfigFrom($path, 'form-submitter');

        if ($this->app->bound(Dispatcher::class)) {
            /** @var Dispatcher $dispatcher */
            $dispatcher = $this->app->get(Dispatcher::class);

            $dispatcher->listen(FormFinished::class, [FormFinishedEventListener::class, 'handle']);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function register(): void
    {
        if (!$this->app->bound(Repository::class)) {
            throw new LogicException(
                sprintf(
                    'Unable to register classes without binding of %s contract to the concrete class.',
                    Repository::class,
                ),
            );
        }

        $this->app->singleton(FormSubmitterFactoryInterface::class, FormSubmitterFactory::class);

        $transactionClass = $this->getConfig('transaction_class');

        if ($transactionClass !== null) {
            $this->registerTransaction($transactionClass);
        }

        $this->app->singleton(FormSubmitterInterface::class, $this->getFormSubmitterConcreteCallback());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getConfig(string $key, mixed $default = null): mixed
    {
        /** @var Repository $config */
        $config = $this->app->get(Repository::class);

        return $config->get("form-submitter.$key", $default);
    }

    private function getFormSubmitterConcreteCallback(): Closure
    {
        return function (): FormSubmitterInterface {
            return new FormSubmitter(
                $this->app->make(FormSubmitterFactoryInterface::class),
                $this->getConfig('submitters', []),
            );
        };
    }

    private function registerTransaction(mixed $transactionClass): void
    {
        if ($transactionClass instanceof TransactionInterface) {
            $concrete = static fn (): TransactionInterface => $transactionClass;
        } else {
            $concrete = $transactionClass;
        }

        $this->app->singleton(TransactionInterface::class, $concrete);
    }
}
