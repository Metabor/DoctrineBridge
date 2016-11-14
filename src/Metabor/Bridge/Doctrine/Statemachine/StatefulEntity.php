<?php
namespace Metabor\Bridge\Doctrine\Statemachine;

use Metabor\Statemachine\Factory\Factory;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Oliver Tischlinger
 */
class StatefulEntity implements \SplObserver
{
    /**
     * @var State
     *
     * @ORM\ManyToOne(targetEntity="Metabor\Bridge\Doctrine\Statemachine\State")
     */
    protected $currentState;

    /**
     * @var \MetaborStd\Statemachine\StatemachineInterface
     */
    private $statemachine;

    /**
     * @return \Metabor\Bridge\Doctrine\Statemachine\Process
     */
    public function getProcess()
    {
        if ($this->currentState) {
            return $this->currentState->getProcess();
        }
    }

    /**
     * @param Process $process
     *
     * @throws \Exception
     */
    public function setProcess(Process $process)
    {
        if (!$this->currentState) {
            $this->currentState = $process->getInitialState();
        } elseif ($this->currentState->getProcess() !== $process) {
            throw new \Exception(
                    'There is still a process runnning! Change the process by setting the currentState to a state from the new process');
        }
    }

    /**
     * @return string
     */
    public function getCurrentStateName()
    {
        $currentState = $this->getCurrentState();
        if ($currentState) {
            return $currentState->getName();
        }
    }

    /**
     * @return \Metabor\Bridge\Doctrine\Statemachine\State
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * @param State $currentState
     */
    protected function setCurrentState(State $currentState)
    {
        $this->currentState = $currentState;
    }

    /**
     * @see SplObserver::update()
     */
    public function update(\SplSubject $subject)
    {
        if ($subject === $this->statemachine) {
            $currentState = $this->statemachine->getCurrentState();
            if ($currentState instanceof State) {
                $this->setCurrentState($currentState);
            } else {
                throw new \LogicException('Current State has to be a '.State::ENTITY_NAME);
            }
        }
    }

    /**
     * Overwrite this to make changes on the created statemachine.
     *
     * @return \MetaborStd\Statemachine\Factory\FactoryInterface
     */
    protected function getStatemachineFactory()
    {
        $detector = new Detector();
        $factory = new Factory($detector, $detector);
        $factory->attachStatemachineObserver($this);

        return $factory;
    }

    /**
     * @return \MetaborStd\Statemachine\StatemachineInterface
     */
    protected function getStatemachine()
    {
        if (!$this->statemachine) {
            $this->statemachine = $this->getStatemachineFactory()->createStatemachine($this);
        }

        return $this->statemachine;
    }

    /**
     * @param string       $eventName
     * @param \ArrayAccess $context
     */
    public function triggerEvent($eventName, \ArrayAccess $context = null)
    {
        $this->getStatemachine()->triggerEvent($eventName, $context);
    }

    /**
     * @param \ArrayAccess $context
     */
    public function checkTransitions(\ArrayAccess $context = null)
    {
        $this->getStatemachine()->checkTransitions($context);
    }
}
