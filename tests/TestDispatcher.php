<?php

declare(strict_types=1);

namespace Lexal\LaravelSteppedFormSubmitter\Tests;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;

use function is_string;

final class TestDispatcher implements Dispatcher
{
    /** @var array<string, Closure|string|array<string|int, mixed>|null> */
    private array $listeners = [];

    /**
     * @param Closure|string|array<string|int, mixed> $events
     * @param Closure|string|array<string|int, mixed>|null $listener
     */
    public function listen($events, $listener = null): void
    {
        if (is_string($events)) {
            $this->listeners[$events] = $listener;
        }
    }

    /**
     * @return Closure|string|array<string|int, mixed>|null
     */
    public function getListener(string $event): Closure|string|array|null
    {
        return $this->listeners[$event] ?? null;
    }

    /**
     * @param string $eventName
     */
    public function hasListeners($eventName): bool
    {
        return isset($this->listeners[$eventName]);
    }

    /**
     * @param  object|string $subscriber
     */
    public function subscribe($subscriber): void
    {
        // nothing to do
    }

    /**
     * @param string|object $event
     * @param mixed $payload
     */
    public function until($event, $payload = []): null
    {
        return null;
    }

    /**
     * @param string|object $event
     * @param mixed $payload
     * @param bool $halt
     *
     * @return array<string|int, mixed>
     */
    public function dispatch($event, $payload = [], $halt = false): array
    {
        return [];
    }

    /**
     * @param string $event
     * @param array<string, mixed> $payload
     */
    public function push($event, $payload = []): void
    {
        // nothing to do
    }

    /**
     * @param string $event
     */
    public function flush($event): void
    {
        // nothing to do
    }

    /**
     * @param string $event
     */
    public function forget($event): void
    {
        // nothing to do
    }

    public function forgetPushed(): void
    {
        // nothing to do
    }
}
