<?php
namespace Metabor\Bridge\Doctrine\Statemachine;
use Doctrine\Common\Collections\ArrayCollection;
use MetaborStd\Statemachine\ProcessInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @author Oliver Tischlinger
 *
 * @ORM\Table()
 * @ORM\Entity
 *
 */
class Process implements ProcessInterface
{
    const ENTITY_NAME = __CLASS__;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(unique=true)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="State", mappedBy="process", indexBy="name", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $states;

    /**
     * @var State
     *
     * @ORM\OneToOne(targetEntity="State", cascade={"persist", "remove"})
     */
    private $initialState;

    /**
     * @param string $name
     * @param State  $initialState
     */
    public function __construct($name = null, State $initialState = null)
    {
        $this->states = new ArrayCollection();
        $this->name = $name;
        if ($initialState) {
            $this->setInitialState($initialState);
        }
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @see \Metabor\Named::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param State $initialState
     */
    public function setInitialState(State $initialState)
    {
        $this->initialState = $initialState;
        $this->addState($initialState);
    }

    /**
     * @see \MetaborStd\Statemachine\ProcessInterface::getInitialState()
     */
    public function getInitialState()
    {
        return $this->initialState;
    }

    /**
     * @see \MetaborStd\Statemachine\StateCollectionInterface::getStates()
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * @param State $state
     */
    public function addState(State $state)
    {
        $state->setProcess($this);
        $this->states->set($state->getName(), $state);
    }

    /**
     * @param State $state
     */
    public function removeState(State $state)
    {
        $this->states->removeElement($state);
    }

    /**
     * @return \Metabor\Bridge\Doctrine\Statemachine\State
     */
    public function getState($name)
    {
        return $this->states->get($name);
    }

    /**
     * @see \MetaborStd\Statemachine\StateCollectionInterface::hasState()
     */
    public function hasState($name)
    {
        return $this->states->containsKey($name);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param  string                                      $stateName
     * @return \Metabor\Bridge\Doctrine\Statemachine\State
     */
    public function findOrCreateState($stateName)
    {
        if ($this->states->containsKey($stateName)) {
            $state = $this->states->get($stateName);
        } else {
            $state = new State($stateName, $this);
            $this->states->set($stateName, $state);
        }

        return $state;
    }

    /**
     * @param Process $process
     * @param bool    $copyObservers
     */
    public function addProcess(Process $process, $copyObservers = true)
    {
        /* @var $copyState State  */
        foreach ($process->getStates() as $copyState) {
            $state = $this->findOrCreateState($copyState->getName());
            $state->setMetadata($copyState->getMetadata());
            /* @var $copyTransiton Transition */
            foreach ($copyState->getTransitions() as $copyTransiton) {
                $targetState = $this->findOrCreateState($copyTransiton->getTargetState()->getName());
                $state->createTransition($targetState, $copyTransiton->getEventName(), $copyTransiton->getConditionName());
            }

            /* @var $copyEvent \Metabor\Bridge\Doctrine\Event\Event */
            foreach ($copyState->getEvents() as $copyEvent) {
                $event = $state->findOrCreateEvent($copyEvent->getName());
                $event->setMetadata($copyEvent->getMetadata());
                if ($copyObservers) {
                    foreach ($copyEvent->getObservers() as $observer) {
                        $event->attach($observer);
                    }
                }
            }
        }
    }

}
