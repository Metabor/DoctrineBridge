<?php
namespace Metabor\Bridge\Doctrine\Statemachine;

use Doctrine\Common\Collections\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Metabor\Bridge\Doctrine\KeyValue\Metadata;
use MetaborStd\Statemachine\StateInterface;
use Doctrine\ORM\Mapping as ORM;
use Metabor\Bridge\Doctrine\Event\Event;

/**
 *
 * @author Oliver Tischlinger
 * 
 * @ORM\Table()
 * @ORM\Entity
 *        
 */
class State extends Metadata implements StateInterface
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
     * @var Process
     */
    private $process;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Metabor\Bridge\Doctrine\Event\Event", indexBy="name", cascade={"persist", "remove"})
     */
    private $events;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Transition", mappedBy="sourceState", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $transitions;

    /**
     * @param string $name
     * @param Process $process
     */
    public function __construct($name = null, Process $process = null)
    {
        $this->name = $name;
        $this->process = $process;
        $this->events = new ArrayCollection();
        $this->transitions = new ArrayCollection();
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
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @see \MetaborStd\Statemachine\StateInterface::getEvent()
     */
    public function getEvent($name)
    {
        return $this->events->offsetGet($name);
    }

    /**
     * @see \MetaborStd\Statemachine\StateInterface::getEventNames()
     */
    public function getEventNames()
    {
        return $this->events->getKeys();
    }

    /**
     * @see \MetaborStd\Statemachine\StateInterface::getTransitions()
     */
    public function getTransitions()
    {
        return $this->getTransitions();
    }

    /**
     * @see \MetaborStd\Statemachine\StateInterface::hasEvent()
     */
    public function hasEvent($name)
    {
        return $this->events->offsetExists($name);
    }

    /**
     * @return \Metabor\Bridge\Doctrine\Statemachine\Process
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param Process $process
     */
    public function setProcess(Process $process)
    {
        $this->process = $process;
    }

    /**
     * @param Collection $events
     */
    public function setEvents(Collection $events)
    {
        $this->events = $events;
    }

    /**
     * @param Transition $transition
     */
    public function addTransition(Transition $transition)
    {
        $transition->setSourceState($this);
        $this->transitions->add($transition);
    }

    /**
     * @param Transition $transition
     */
    public function removeTransition(Transition $transition)
    {
        $this->transitions->remove($transition);
    }

    /**
     * @param unknown_type $eventName
     * @return \Metabor\Bridge\Doctrine\Event\Event
     */
    public function findOrCreateEvent($eventName)
    {
        if ($this->events->offsetExists($eventName)) {
            $event = new Event($eventName);
            $this->events->offsetSet($eventName, $event);
        } else {
            $event = $this->events->offsetGet($eventName);
        }
        return $event;
    }

    /**
     * @param State $targetState
     * @param string $eventName
     * @param string $conditionName
     * @return \Metabor\Bridge\Doctrine\Statemachine\Transition
     */
    public function createTransition(State $targetState, $eventName = null, $conditionName = null)
    {
        $tansition = new Transition($this, $targetState, $eventName, $conditionName);
        $this->transitions->add($tansition);
        return $tansition;
    }

}
