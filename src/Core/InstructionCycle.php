<?php
declare(strict_types=1);
namespace Meanly\Mdk\Kernel\Core;
use Meanly\Mdk\Kernel\Contracts\StateInterface;
use Meanly\Mdk\Kernel\Contracts\EventInterface;
use Meanly\Mdk\Kernel\Identity\CanonicalJsonEncoder;
class InstructionCycle {
    private CanonicalJsonEncoder $encoder;
    public function __construct() { $this->encoder = new CanonicalJsonEncoder(); }
    public function execute(StateInterface $currentState, EventInterface $event, callable $reducer): StateInterface {
        if ($event->getPreviousHash() !== $currentState->getStateRoot()) throw new \RuntimeException("Chain Divergence.");
        $newState = $reducer($currentState, $event);
        if ($newState === $currentState) throw new \LogicException("Mutation detected.");
        return $newState;
    }
}
