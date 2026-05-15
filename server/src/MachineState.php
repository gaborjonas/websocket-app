<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp;

enum MachineState: string
{
    case Idle = 'Idle';
    case Producing = 'Producing';
    case Starved = 'Starved';
}