<?php

namespace Core\Events;

interface Listener
{
    public function handle(object $event): void;
}