<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\ServiceProvider;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Lexal\FormSubmitter\FormSubmitter;
use Lexal\FormSubmitter\FormSubmitterInterface;
use Lexal\FormSubmitter\Transaction\TransactionInterface;
use Lexal\FormSubmitter\TransactionalFormSubmitter;
use Lexal\LaravelSteppedFormSubmitter\EventListener\FormFinishedEventListener;
use Lexal\SteppedForm\EventDispatcher\Event\FormFinished;
use LogicException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_map;
use function dirname;
use function is_string;
use function sprintf;

class ServiceProvider extends LaravelServiceProvider
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

        $this->app->singleton(FormSubmitterInterface::class, function () {
            $submitters = $this->getConfig('submitters', []);

            if (!$submitters) {
                throw new BindingResolutionException(
                    'You must register at least one submitter to be able to submit form.',
                );
            }

            $formSubmitter = new FormSubmitter(
                ...array_map(fn (mixed $submitter) => $this->getInstance($submitter), $submitters),
            );

            $useTransactional = $this->getConfig('use_transactional', false);

            if ($useTransactional) {
                $formSubmitter = new TransactionalFormSubmitter(
                    $formSubmitter,
                    $this->app->make(TransactionInterface::class),
                );
            }

            return $formSubmitter;
        });
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

    /**
     * @throws BindingResolutionException
     */
    private function getInstance(mixed $instance): mixed
    {
        return is_string($instance) ? $this->app->make($instance) : $instance;
    }
}
