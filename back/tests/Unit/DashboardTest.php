<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Ratchet\ConnectionInterface;
use Gabor\WebsocketApp\Dashboard;
use Gabor\WebsocketApp\MachineState;

#[CoversClass(Dashboard::class)]
final class DashboardTest extends TestCase
{
    private Dashboard $dashboard;

    protected function setUp(): void
    {
        $this->dashboard = new Dashboard();
    }

    #[Test]
    #[TestDox('Dashboard starts with no clients')]
    public function itStartsWithNoClients(): void
    {
        $this->assertCount(0, $this->dashboard->getClients());
    }

    #[Test]
    #[TestDox('Dashboard can add client')]
    public function itCanAddClient(): void
    {
        $client = $this->createMock(ConnectionInterface::class);
        
        $this->dashboard->addClient($client);

        $this->assertCount(1, $this->dashboard->getClients());
        $this->assertSame([$client], $this->dashboard->getClients());
    }

    #[Test]
    #[TestDox('Dashboard can remove client')]
    public function itCanRemoveClient(): void
    {
        $client = $this->createMock(ConnectionInterface::class);
        
        $this->dashboard->addClient($client);
        $this->dashboard->removeClient($client);

        $this->assertCount(0, $this->dashboard->getClients());
    }

    #[Test]
    #[TestDox('Dashboard can add multiple clients')]
    public function itCanAddMultipleClients(): void
    {
        $client1 = $this->createMock(ConnectionInterface::class);
        $client2 = $this->createMock(ConnectionInterface::class);
        
        $this->dashboard->addClient($client1);
        $this->dashboard->addClient($client2);

        $this->assertCount(2, $this->dashboard->getClients());
        $this->assertSame([$client1, $client2], $this->dashboard->getClients());
    }

    #[Test]
    #[TestDox('Dashboard does not add duplicate clients')]
    public function itDoesNotAddDuplicateClients(): void
    {
        $client = $this->createMock(ConnectionInterface::class);
        
        $this->dashboard->addClient($client);
        $this->dashboard->addClient($client);

        $this->assertCount(1, $this->dashboard->getClients());
        $this->assertSame([$client], $this->dashboard->getClients());
    }

    #[Test]
    #[TestDox('Dashboard can remove specific client when multiple added')]
    public function itCanRemoveSpecificClientWhenMultipleAdded(): void
    {
        $client1 = $this->createMock(ConnectionInterface::class);
        $client2 = $this->createMock(ConnectionInterface::class);
        $client3 = $this->createMock(ConnectionInterface::class);
        
        $this->dashboard->addClient($client1);
        $this->dashboard->addClient($client2);
        $this->dashboard->addClient($client3);
        
        $this->dashboard->removeClient($client2);

        $this->assertCount(2, $this->dashboard->getClients());
        $this->assertSame([0 => $client1, 2 => $client3], $this->dashboard->getClients());

    }

    #[Test]
    #[TestDox('Dashboard update sends message to all clients')]
    public function itUpdateSendsMessageToAllClients(): void
    {
        $client1 = $this->createMock(ConnectionInterface::class);
        $client2 = $this->createMock(ConnectionInterface::class);
        
        $expectedPayload = json_encode([
            'machine' => 'Test Machine',
            'state' => 'Producing'
        ], JSON_THROW_ON_ERROR);
        
        $client1->expects($this->once())
            ->method('send')
            ->with($this->equalTo($expectedPayload));
        
        $client2->expects($this->once())
            ->method('send')
            ->with($this->equalTo($expectedPayload));
        
        $this->dashboard->addClient($client1);
        $this->dashboard->addClient($client2);
        
        $this->dashboard->update(MachineState::Producing, 'Test Machine');
    }

    #[Test]
    #[TestDox('Dashboard update does not send to removed clients')]
    public function itUpdateDoesNotSendToRemovedClients(): void
    {
        $client1 = $this->createMock(ConnectionInterface::class);
        $client2 = $this->createMock(ConnectionInterface::class);
        
        $expectedPayload = json_encode([
            'machine' => 'Test Machine',
            'state' => 'Idle'
        ], JSON_THROW_ON_ERROR);
        
        $client1->expects($this->once())
            ->method('send')
            ->with($this->equalTo($expectedPayload));
        
        $client2->expects($this->never())
            ->method('send');
        
        $this->dashboard->addClient($client1);
        $this->dashboard->addClient($client2);
        $this->dashboard->removeClient($client2);
        
        $this->dashboard->update(MachineState::Idle, 'Test Machine');
    }

    #[Test]
    #[TestDox('Dashboard update sends correct payload format')]
    public function itUpdateSendsCorrectPayloadFormat(): void
    {
        $client = $this->createMock(ConnectionInterface::class);
        
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (string $payload) {
                $data = json_decode($payload, true);
                
                return is_array($data) &&
                    isset($data['machine'], $data['state']) 
                     &&
                    $data['machine'] === 'Test Machine' &&
                    $data['state'] === 'Starved';
            }));
        
        $this->dashboard->addClient($client);
        $this->dashboard->update(MachineState::Starved, 'Test Machine');
    }

    #[Test]
    #[TestDox('Dashboard update handles all machine states')]
    public function itUpdateHandlesAllMachineStates(): void
    {
        $client = $this->createMock(ConnectionInterface::class);
        
        $client->expects($this->exactly(count(MachineState::cases())))
            ->method('send')
            ->with($this->callback(function (string $payload) {
                $data = json_decode($payload, true);

                return is_array($data) && isset($data['machine'], $data['state']);
            }));
        
        $this->dashboard->addClient($client);
        
        foreach (MachineState::cases() as $state) {
            $this->dashboard->update($state, 'Test Machine');
        }
    }

    #[Test]
    #[TestDox('Dashboard handles removing non-existent client gracefully')]
    public function itHandlesRemovingNonExistentClientGracefully(): void
    {
        $client1 = $this->createMock(ConnectionInterface::class);
        $client2 = $this->createMock(ConnectionInterface::class);
        
        $this->dashboard->addClient($client1);
        $this->dashboard->removeClient($client2);

        $this->assertCount(1, $this->dashboard->getClients());
        $this->assertSame([$client1], $this->dashboard->getClients());
    }
}
