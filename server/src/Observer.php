<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp;

abstract class Observer
{
    public readonly string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    abstract public function update(MachineState $state, string $from): void;
}
