<?php

namespace Core\Events;

class EventDispatcher
{
    protected array $listeners = [];

    /**
     * Register a listener for the given event.
     *
     * @param string $event   The event name
     * @param callable $listener The listener function
     *
     * @return void
     */
    public function listen(string $event, callable $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    /**
     * Dispatches a given event to all the registered listeners.
     *
     * @param object $event The event object to dispatch
     *
     * @return void
     */
    public function dispatch(object $event): void
    {
        $eventName = get_class($event);
        foreach ($this->listeners[$eventName] ?? [] as $listener) {
            call_user_func($listener, $event);
        }
    }
}