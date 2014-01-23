<?php
namespace Metabor\Bridge\Doctrine\Statemachine;
use Metabor\Statemachine\Factory\Factory;

use Metabor\Statemachine\Statemachine;

use MetaborStd\Statemachine\StatefulInterface;

use Doctrine\Common\Collections\ArrayCollection;

use MetaborStd\Statemachine\ProcessInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @author Oliver Tischlinger
 * 
 */
class StatefulEntity
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
        return $this->currentState->getProcess();
    }

    /**
     * 
     * @param Process $process
     * @throws \Exception
     */
    public function setProcess(Process $process)
    {
        if (!$this->currentState) {
            $this->currentState = $process->getInitialState();
        } elseif ($this->currentState->getProcess() !== $process) {
            throw new \Exception('There is still a process runnning! Change the process by setting the currentState to a state from the new process');
        }
    }

    /**
     * @return State;
     */
    public function getCurrentStateName()
    {
        if (!$this->currentState) {
            return $this->currentState->getName();
        }
    }

    /**
     * @return State;
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
            $this->setCurrentState($this->statemachine->getCurrentState());
        }
    }

    /**
     * Overwrite this to make changes on the created statemachine
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
     * @param string $eventName
     * @param \ArrayAccess $context
     */
    public function trigger($eventName, \ArrayAccess $context = null)
    {
        $this->getStatemachine()->triggerEvent($eventName, $context);
    }

}
