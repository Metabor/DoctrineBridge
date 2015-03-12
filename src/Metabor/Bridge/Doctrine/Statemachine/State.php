<?php
namespace Metabor\Bridge\Doctrine\Statemachine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Metabor\Bridge\Doctrine\Event\Event;
use MetaborStd\MetadataInterface;
use MetaborStd\Statemachine\StateInterface;

/**
 * @author Oliver Tischlinger
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class State implements StateInterface, \ArrayAccess, MetadataInterface
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
     *
     * @ORM\ManyToOne(targetEntity="Process", inversedBy="states")
     * @ORM\JoinColumn(nullable=false)
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
     * @var array
     *
     * @ORM\Column( type="array" )
     */
    private $metadata = array();

    /**
     * @param string  $name
     * @param Process $process
     */
    public function __construct($name = null, Process $process = null)
    {
        $this->name = $name;
        $this->process = $process;
        $this->events = new ArrayCollection();
        $this->transitions = new ArrayCollection();

        if ($process) {
            $process->addState($this);
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
        return $this->events->get($name);
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
        return $this->transitions;
    }

    /**
     * @see \MetaborStd\Statemachine\StateInterface::hasEvent()
     */
    public function hasEvent($name)
    {
        return $this->events->containsKey($name);
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
     * @param Event $event
     */
    public function addEvent(Event $event)
    {
        $this->events->set($event->getName(), $event);
    }

    /**
     * @param Event $event
     */
    public function removeEvent(Event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * @param Process $process
     */
    public function setProcess(Process $process)
    {
        $this->process = $process;
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
        $this->transitions->removeElement($transition);
    }

    /**
     * @param string $eventName
     *
     * @return \Metabor\Bridge\Doctrine\Event\Event
     */
    public function findOrCreateEvent($eventName)
    {
        if ($eventName) {
            if ($this->events->containsKey($eventName)) {
                $event = $this->events->get($eventName);
            } else {
                $event = new Event($eventName);
                $this->events->set($eventName, $event);
            }

            return $event;
        }
    }

    /**
     * @param State  $targetState
     * @param string $eventName
     * @param string $conditionName
     *
     * @return \Metabor\Bridge\Doctrine\Statemachine\Transition
     */
    public function createTransition(State $targetState, $eventName = null, $conditionName = null)
    {
        $event = $this->findOrCreateEvent($eventName);
        $tansition = new Transition($this, $targetState, $event, $conditionName);
        $this->transitions->add($tansition);

        return $tansition;
    }

    /**
     * @param string $targetStateName
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransitionsTo($targetStateName)
    {
        $filter = function (Transition $transition) use ($targetStateName) {
            return ($transition->getTargetState()->getName() === $targetStateName);
        };

        return $this->transitions->filter($filter);
    }

    /**
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    /**
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        if (isset($this->metadata[$offset])) {
            return $this->metadata[$offset];
        }
    }

    /**
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        $this->metadata[$offset] = $value;
    }

    /**
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }
}
