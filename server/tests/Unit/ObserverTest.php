<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Gabor\WebsocketApp\Employee;
use Gabor\WebsocketApp\MachineState;
use Gabor\WebsocketApp\Observer;

#[CoversClass(Observer::class)]
final class ObserverTest extends TestCase
{
    #[Test]
    #[TestDox('Observer name is set in constructor')]
    public function itHasNameSetInConstructor(): void
    {
        $name = 'Production Line Worker';
        $observer = new Employee($name);
        
        $this->assertSame($name, $observer->name);
    }

    #[Test]
    #[TestDox('Observer update method handles different machine states')]
    public function itUpdateMethodHandlesDifferentMachineStates(): void
    {
        $observer = new Employee('Test Employee');
        $machineName = 'Test Machine';
        
        foreach (MachineState::cases() as $state) {
            $observer->update($state, $machineName);
        }
        
        $this->assertTrue(true);
    }

    #[Test]
    #[TestDox('Observer can be extended with custom implementation')]
    public function itCanBeExtendedWithCustomImplementation(): void
    {
        $customObserver = new class extends Observer {
            private array $updates = [];

            public function __construct()
            {
                parent::__construct('Custom Observer');
            }

            public function update(MachineState $state, string $from): void
            {
                $this->updates[] = ['state' => $state, 'from' => $from];
            }

            public function getUpdates(): array
            {
                return $this->updates;
            }
        };

        $this->assertInstanceOf(Observer::class, $customObserver);
        $this->assertSame('Custom Observer', $customObserver->name);

        $customObserver->update(MachineState::Producing, 'Test Machine');
        $updates = $customObserver->getUpdates();

        $this->assertCount(1, $updates);
        $this->assertSame(MachineState::Producing, $updates[0]['state']);
        $this->assertSame('Test Machine', $updates[0]['from']);
    }
}
