<?php

declare(strict_types=1);

use Gabor\WebsocketApp\Employee;
use Gabor\WebsocketApp\Machine;
use Gabor\WebsocketApp\MachineState;

require_once dirname(__DIR__).'/vendor/autoload.php';

$machineA = new Machine('Machine A');
$machineB = new Machine('Machine B');
$machineC = new Machine('Machine C');

$alice = new Employee('Alice');
$bob   = new Employee('Bob');
$carol = new Employee('Carol');

$machineA->attach($alice);
$machineA->attach($bob);

$machineB->attach($alice);
$machineB->attach($bob);

$machineC->attach($alice);
$machineC->attach($carol);

echo '=== Production Line State Changes ===' . PHP_EOL;

$machineA->setState(MachineState::Producing);
echo PHP_EOL;
$machineB->setState(MachineState::Starved);
echo PHP_EOL;
$machineC->setState(MachineState::Producing);
echo PHP_EOL;
$machineA->setState(MachineState::Starved);
echo PHP_EOL;
$machineB->setState(MachineState::Producing);
echo PHP_EOL;
$machineC->setState(MachineState::Idle);
