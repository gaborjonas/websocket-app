<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp;

class Machine extends Subject
{
    public function __construct(
        private readonly string $name
    )
    {
       parent::__construct();
    }

    public function getName(): string
    {
        return $this->name;
    }
}
