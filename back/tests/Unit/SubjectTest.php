<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Gabor\WebsocketApp\Employee;
use Gabor\WebsocketApp\Machine;
use Gabor\WebsocketApp\MachineState;
use Gabor\WebsocketApp\Observer;
use Gabor\WebsocketApp\Subject;

#[CoversClass(Subject::class)]
final class SubjectTest extends TestCase
{
    private Machine $subject;

    protected function setUp(): void
    {
        $this->subject = new Machine('Test Machine');
    }

    #[Test]
    #[TestDox('Subject starts with no observers')]
    public function itStartsWithNoObservers(): void
    {
        $this->assertEmpty($this->subject->getObservers());
    }

    #[Test]
    #[TestDox('Subject can attach observer')]
    public function itCanAttachObserver(): void
    {
        $observer = new Employee('Test Employee');
        
        $this->subject->attach($observer);
        
        $this->assertCount(1, $this->subject->getObservers());
        $this->assertTrue(in_array($observer, $this->subject->getObservers(), true));
    }

    #[Test]
    #[TestDox('Subject can detach observer')]
    public function itCanDetachObserver(): void
    {
        $observer = new Employee('Test Employee');
        
        $this->subject->attach($observer);
        $this->subject->detach($observer);
        
        $this->assertCount(0, $this->subject->getObservers());
    }

    #[Test]
    #[TestDox('Subject can attach multiple observers')]
    public function itCanAttachMultipleObservers(): void
    {
        $observer1 = new Employee('Employee 1');
        $observer2 = new Employee('Employee 2');
        
        $this->subject->attach($observer1);
        $this->subject->attach($observer2);
        
        $this->assertEquals([$observer1, $observer2], $this->subject->getObservers());
    }

    #[Test]
    #[TestDox('Subject does not attach duplicate observer')]
    public function itDoesNotAttachDuplicateObserver(): void
    {
        $observer = new Employee('Test Employee');
        
        $this->subject->attach($observer);
        $this->subject->attach($observer);

        $this->assertCount(1, $this->subject->getObservers());
        $this->assertTrue(in_array($observer, $this->subject->getObservers(), true));
    }

    #[Test]
    #[TestDox('Subject can detach specific observer when multiple attached')]
    public function itCanDetachSpecificObserverWhenMultipleAttached(): void
    {
        $observer1 = new Employee('Employee 1');
        $observer2 = new Employee('Employee 2');
        $observer3 = new Employee('Employee 3');
        
        $this->subject->attach($observer1);
        $this->subject->attach($observer2);
        $this->subject->attach($observer3);
        
        $this->subject->detach($observer2);

        $this->assertCount(2, $this->subject->getObservers());
        $this->assertEquals([0 => $observer1, 2 => $observer3], $this->subject->getObservers());
    }

    #[Test]
    #[TestDox('Subject notifies observers on state change')]
    public function itNotifiesObserversOnStateChange(): void
    {
        $observer = $this->createMock(Observer::class);
        $observer->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo(MachineState::Producing),
                $this->equalTo('Test Machine')
            );
        
        $this->subject->attach($observer);
        $this->subject->setState(MachineState::Producing);
    }

    #[Test]
    #[TestDox('Subject notifies all observers on state change')]
    public function itNotifiesAllObserversOnStateChange(): void
    {
        $observer1 = $this->createMock(Observer::class);
        $observer2 = $this->createMock(Observer::class);
        
        $observer1->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo(MachineState::Producing),
                $this->equalTo('Test Machine')
            );
        
        $observer2->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo(MachineState::Producing),
                $this->equalTo('Test Machine')
            );
        
        $this->subject->attach($observer1);
        $this->subject->attach($observer2);
        $this->subject->setState(MachineState::Producing);
    }

    #[Test]
    #[TestDox('Subject does not notify detached observers')]
    public function itDoesNotNotifyDetachedObservers(): void
    {
        $observer1 = $this->createMock(Observer::class);
        $observer2 = $this->createMock(Observer::class);
        
        $observer1->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo(MachineState::Producing),
                $this->equalTo('Test Machine')
            );
        
        $observer2->expects($this->never())
            ->method('update');
        
        $this->subject->attach($observer1);
        $this->subject->attach($observer2);
        $this->subject->detach($observer2);
        $this->subject->setState(MachineState::Producing);
    }

    #[Test]
    #[TestDox('Subject handles detaching non-existent observer gracefully')]
    public function itHandlesDetachingNonExistentObserverGracefully(): void
    {
        $observer1 = new Employee('Employee 1');
        $observer2 = new Employee('Employee 2');
        
        $this->subject->attach($observer1);
        
        // Should not throw exception
        $this->subject->detach($observer2);
        
        $this->assertCount(1, $this->subject->getObservers());
        $this->assertSame([$observer1], $this->subject->getObservers());
    }
}
