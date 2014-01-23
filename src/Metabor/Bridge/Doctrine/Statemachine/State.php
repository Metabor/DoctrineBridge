<?php
namespace Metabor\Bridge\Doctrine\Statemachine;

use Doctrine\Common\Collections\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Metabor\Bridge\Doctrine\KeyValue\Metadata;
use MetaborStd\Statemachine\StateInterface;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToMany(targetEntity="Event", indexBy="name")
     */
    private $events;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Transition")
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

}
