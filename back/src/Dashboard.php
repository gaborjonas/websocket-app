<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp;

use Ratchet\ConnectionInterface;

class Dashboard extends Observer
{
    /** @var array<int, ConnectionInterface> */
    private array $clients = [];

    public function __construct()
    {
        parent::__construct('Dashboard');
    }

    public function addClient(ConnectionInterface $client): void
    {
        if (in_array($client, $this->clients, true)) {
            return;
        }
        
        $this->clients[] = $client;
    }

    public function removeClient(ConnectionInterface $client): void
    {
        $this->clients = array_filter(
            $this->clients,
            fn(ConnectionInterface $c) => $c !== $client
        );
    }

    /**
     * @return array<int, ConnectionInterface>
     */
    public function getClients(): array
    {
        return $this->clients;
    }

    public function update(MachineState $state, string $from): void
    {
        $payload = json_encode([
            'machine' => $from,
            'state' => $state->value
        ], JSON_THROW_ON_ERROR);

        foreach ($this->clients as $client) {
            $client->send($payload);
        }
    }
}
