<?php
namespace Metabor\Doctrine\Statemachine;
use Doctrine\Common\Collections\ArrayCollection;

use Metabor\Doctrine\KeyValue\Metadata;

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
     * @param string $name
     * @param Process $process
     */
    public function __construct($name = null, Process $process = null)
    {
        $this->name = $name;
        $this->events = new ArrayCollection();
        $this->process = $process;
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

    public function getTransitions()
    {
        // TODO Auto-generated method stub
    }

    /**
     * @see \MetaborStd\Statemachine\StateInterface::hasEvent()
     */
    public function hasEvent($name)
    {
        return $this->events->offsetExists($name);
    }

}
