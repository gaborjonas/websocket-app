<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp;

use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

final class Application implements MessageComponentInterface
{
    /** @var array<int, Machine> */
    private array $machines = [];

    public function __construct(
        private Dashboard $dashboard,
    )
    {
    }

    public function addMachine(Machine ...$machines): void
    {
        foreach ($machines as $machine) {
            $this->machines[] = $machine;
        }

        foreach ($this->machines as $machine) {
            $machine->attach($this->dashboard);
        }
    }

    /**
     * @return array<int, Machine>
     */
    public function getMachines(): array
    {
        return $this->machines;
    }

    public function onOpen(ConnectionInterface $connection): void
    {
        $this->dashboard->addClient($connection);
        /** @phpstan-ignore-next-line */
        echo sprintf('Client #%s connected', $connection->resourceId) . PHP_EOL;

        // Send current state of all machines to the newly connected client
        $data = [
            'id' => $connection->resourceId,
            'machines' => array_map(function (Machine $machine) {
                return [
                    'id' => str_replace(' ', '-', $machine->getName()) |> strtolower(...),
                    'name' => $machine->getName(),
                    'state' => $machine->state->value,
                ];
            }, array_values($this->machines)),
        ];

        $connection->send(json_encode($data, JSON_THROW_ON_ERROR));
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        // No messages from client
    }

    public function onClose(ConnectionInterface $connection): void
    {
        $this->dashboard->removeClient($connection);
        /** @phpstan-ignore-next-line */
        echo printf('Client #%s disconnected', $connection->resourceId) . PHP_EOL;
    }

    public function onError(ConnectionInterface $connection, Exception $e): void
    {
        /** @phpstan-ignore-next-line */
        echo printf('An error has occurred on client #%s', $connection->resourceId) . PHP_EOL;
        $connection->close();
    }

    public function randomMachineStateChange(): void
    {
        $states = MachineState::cases();
        $machine = $this->machines[array_rand($this->machines)];
        $newState = $states[array_rand($states)];

        $machine->setState($newState);

        echo sprintf('Setting %s → %s', $machine->getName(), $newState->value) . PHP_EOL;
    }
}