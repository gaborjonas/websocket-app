<?php

declare(strict_types=1);

use Gabor\WebsocketApp\Application;
use Gabor\WebsocketApp\Dashboard;
use Gabor\WebsocketApp\Machine;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require_once dirname(__DIR__).'/vendor/autoload.php';

const ADDRESS = '0.0.0.0';
const PORT = 8080;
const MACHINE_STATE_INTERVAL = 5.0;

$dashboard = new Dashboard();

$app = new Application($dashboard);
$app->addMachine(
    new Machine('Machine A'),
    new Machine('Machine B'),
    new Machine('Machine C'),
);

$server = IoServer::factory(
    new HttpServer(new WsServer($app)),
    PORT,
    ADDRESS,
);
$server->loop->addPeriodicTimer(
    interval: MACHINE_STATE_INTERVAL,
    callback: fn() => $app->randomMachineStateChange(),
);

echo sprintf("WebSocket server listening on ws://%s:%s\n", ADDRESS, PORT);

$server->run();
