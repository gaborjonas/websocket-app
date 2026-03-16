<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp;

class Employee extends Observer
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function update(MachineState $state, string $from): void
    {
        echo sprintf(
            "[Employee] %s — %s is now: %s\n",
            $this->name,
            $from,
            $state->value,
        );
    }
}
