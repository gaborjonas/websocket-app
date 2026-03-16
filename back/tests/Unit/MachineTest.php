<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Gabor\WebsocketApp\Machine;
use Gabor\WebsocketApp\MachineState;

#[CoversClass(Machine::class)]
final class MachineTest extends TestCase
{
    #[Test]
    #[TestDox('Machine can be created with name')]
    public function itCanBeCreatedWithName(): void
    {
        $machine = new Machine('Test Machine');
        
        $this->assertSame(MachineState::Idle, $machine->state);
    }

    #[Test]
    #[TestDox('Machine name is accessible as public property')]
    public function itHasAccessibleNameProperty(): void
    {
        $machine = new Machine('Production Line A');
        
        $this->assertSame('Production Line A', $machine->getName());
    }

    #[Test]
    #[TestDox('Machine initial state is Idle')]
    public function itHasInitialStateIdle(): void
    {
        $machine = new Machine('Test Machine');
        
        $this->assertSame(MachineState::Idle, $machine->state);
    }

    #[Test]
    #[TestDox('Machine can change state to Producing')]
    public function itCanChangeStateToProducing(): void
    {
        $machine = new Machine('Test Machine');
        
        $machine->setState(MachineState::Producing);
        
        $this->assertSame(MachineState::Producing, $machine->state);
    }

    #[Test]
    #[TestDox('Machine can change state to Starved')]
    public function itCanChangeStateToStarved(): void
    {
        $machine = new Machine('Test Machine');
        
        $machine->setState(MachineState::Starved);
        
        $this->assertSame(MachineState::Starved, $machine->state);
    }

    #[Test]
    #[TestDox('Machine can change state back to Idle')]
    public function itCanChangeStateBackToIdle(): void
    {
        $machine = new Machine('Test Machine');
        
        $machine->setState(MachineState::Producing);
        $machine->setState(MachineState::Idle);
        
        $this->assertSame(MachineState::Idle, $machine->state);
    }

    #[DataProvider('provideItCanTransitionBetweenStatesCases')]
    #[Test]
    #[TestDox('Machine can transition from "$fromState" to "$toState"')]
    public function itCanTransitionBetweenStates(MachineState $fromState, MachineState $toState): void
    {
        $machine = new Machine('Test Machine');
        
        $machine->setState($fromState);
        $machine->setState($toState);
        
        $this->assertSame($toState, $machine->state);
    }

    /**
     * @return array<string, array{fromState: MachineState, toState: MachineState}>
     */
    public static function provideItCanTransitionBetweenStatesCases(): iterable
    {
        $states = MachineState::cases();
        $transitions = [];
        
        foreach ($states as $fromState) {
            foreach ($states as $toState) {
                $transitionName = sprintf(
                    'From %s to %s',
                    $fromState->value,
                    $toState->value
                );
                $transitions[$transitionName] = [
                    'fromState' => $fromState,
                    'toState' => $toState,
                ];
            }
        }
        
        return $transitions;
    }

    #[Test]
    #[TestDox('Machine state changes are persistent')]
    public function itHasPersistentStateChanges(): void
    {
        $machine = new Machine('Test Machine');
        
        $machine->setState(MachineState::Producing);
        $this->assertSame(MachineState::Producing, $machine->state);
        
        $machine->setState(MachineState::Starved);
        $this->assertSame(MachineState::Starved, $machine->state);
        
        $machine->setState(MachineState::Idle);
        $this->assertSame(MachineState::Idle, $machine->state);
    }

    #[Test]
    #[TestDox('Machine can handle setting same state multiple times')]
    public function itCanHandleSettingSameStateMultipleTimes(): void
    {
        $machine = new Machine('Test Machine');
        
        $machine->setState(MachineState::Producing);
        $this->assertSame(MachineState::Producing, $machine->state);
        
        $machine->setState(MachineState::Producing);
        $this->assertSame(MachineState::Producing, $machine->state);
        
        $machine->setState(MachineState::Producing);
        $this->assertSame(MachineState::Producing, $machine->state);
    }

    #[Test]
    #[TestDox('Machine extends Subject abstract class')]
    public function itExtendsSubject(): void
    {
        $machine = new Machine('Test Machine');
        
        $this->assertInstanceOf(\Gabor\WebsocketApp\Subject::class, $machine);
    }

    #[Test]
    #[TestDox('Machine getName method returns name')]
    public function itGetNameReturnsName(): void
    {
        $machine = new Machine('Test Machine Name');
        
        $this->assertSame('Test Machine Name', $machine->getName());
    }

    #[Test]
    #[TestDox('Machine handles empty name gracefully')]
    public function itHandlesEmptyNameGracefully(): void
    {
        $machine = new Machine('');
        
        $this->assertSame('', $machine->getName());
    }

    #[Test]
    #[TestDox('Machine handles special characters in name')]
    public function itHandlesSpecialCharactersInName(): void
    {
        $name = 'Machine-123_@#$';
        $machine = new Machine($name);
        
        $this->assertSame($name, $machine->getName());
    }
}
