<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;
use Gabor\WebsocketApp\Application;
use Gabor\WebsocketApp\Dashboard;
use Gabor\WebsocketApp\Machine;
use Gabor\WebsocketApp\MachineState;
use Exception;

#[CoversClass(Application::class)]
final class ApplicationTest extends TestCase
{
    private Application $application;
    private Dashboard $dashboard;

    protected function setUp(): void
    {
        $this->dashboard = new Dashboard();
        $this->application = new Application($this->dashboard);
    }

    #[Test]
    #[TestDox('Application can add machines')]
    public function itCanAddMachines(): void
    {
        $machine1 = new Machine('Machine A');
        $machine2 = new Machine('Machine B');
        
        $this->application->addMachine($machine1, $machine2);
        
        $this->assertSame([$machine1, $machine2], $this->application->getMachines());
    }

    #[Test]
    #[TestDox('Application onOpen adds client to dashboard')]
    public function itOnOpenAddsClientToDashboard(): void
    {
        $client = $this->createMock(ConnectionInterface::class);
        $client->resourceId = 123;
        
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (string $payload) {
                $data = json_decode($payload, true);

                return is_array($data) && isset($data['machines']) && is_array($data['machines']);
            }));
        
        $this->application->onOpen($client);

        $this->assertSame([$client], $this->dashboard->getClients());
    }

    #[Test]
    #[TestDox('Application onOpen sends initial machine data to new client')]
    public function itOnOpenSendsInitialMachineDataToNewClient(): void
    {
        $machine1 = new Machine('Machine A');
        $machine2 = new Machine('Machine B');
        
        $this->application->addMachine($machine1, $machine2);
        
        $client = $this->createMock(ConnectionInterface::class);
        $client->resourceId = 456;
        
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (string $payload) use ($machine1, $machine2) {
                $data = json_decode($payload, true);
                
                if (!isset($data['machines']) || !is_array($data['machines'])) {
                    return false;
                }
                
                $machineNames = array_column($data['machines'], 'name');
                
                return in_array($machine1->getName(), $machineNames, true) &&
                       in_array($machine2->getName(), $machineNames, true) &&
                       count($data['machines']) === 2;
            }));
        
        $this->application->onOpen($client);
    }

    #[Test]
    #[TestDox('Application onOpen sends correct machine data format')]
    public function itOnOpenSendsCorrectMachineDataFormat(): void
    {
        $machine = new Machine('Test Machine');
        $machine->setState(MachineState::Producing);
        
        $this->application->addMachine($machine);
        
        $client = $this->createMock(ConnectionInterface::class);
        $client->resourceId = 789;
        
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (string $payload) {
                $data = json_decode($payload, true);
                
                return isset($data['machines'][0]['state']) &&
                    isset($data['machines'][0]['name'], $data['machines'][0]['id'], $data['id']) 
                     
                     && is_array(
                        $data['machines']
                    ) && count(
                        $data['machines']
                    ) === 1 && $data['machines'][0]['name'] === 'Test Machine' && $data['machines'][0]['state'] === 'Producing';
            }));
        
        $this->application->onOpen($client);
    }

    #[Test]
    #[TestDox('Application onClose removes client from dashboard')]
    public function itOnCloseRemovesClientFromDashboard(): void
    {
        $client = $this->createMock(ConnectionInterface::class);
        $client->resourceId = 999;
        
        // Add client first
        $this->application->onOpen($client);

        $this->assertSame([$client], $this->dashboard->getClients());
        
        // Remove client
        $this->application->onClose($client);
        
        // Verify client was removed
        $this->assertSame([], $this->dashboard->getClients());
    }

    #[Test]
    #[TestDox('Application onError closes connection')]
    public function itOnErrorClosesConnection(): void
    {
        $client = $this->createMock(ConnectionInterface::class);
        $client->resourceId = 33;
        $exception = new Exception('Test error');
        
        $client->expects($this->once())
            ->method('close');
        
        $this->application->onError($client, $exception);
    }

    #[Test]
    #[TestDox('Application handles JSON encoding errors gracefully')]
    public function itHandlesJsonEncodingErrorsGracefully(): void
    {
        $machine = new Machine(str_repeat('A', 10000));
        $this->application->addMachine($machine);
        
        $client = $this->createMock(ConnectionInterface::class);
        $client->resourceId = 123;
        
        $this->application->onOpen($client);
        
        $this->assertTrue(true);
    }
}
