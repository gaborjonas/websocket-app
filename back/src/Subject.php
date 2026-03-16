<?php

declare(strict_types=1);

namespace Gabor\WebsocketApp;

abstract class Subject
{
    public MachineState $state;

    /** @var array<int, Observer> */
    private array $observers = [];

    public function __construct()
    {
        $this->state = MachineState::Idle;
    }

    public function setState(MachineState $state): void
    {
        $this->state = $state;
        $this->notifyAllObservers();
    }

    public function attach(Observer $observer): void
    {
        if (in_array($observer, $this->observers, true)) {
            return;
        }
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer): void
    {
        $this->observers = array_filter(
            $this->observers,
            fn(Observer $o) => $o !== $observer
        );
    }

    /**
     * @return array<int, Observer>
     */
    public function getObservers(): array
    {
        return $this->observers;
    }

    public function notifyAllObservers(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this->state, $this->getName());
        }
    }

    abstract public function getName(): string;
}
