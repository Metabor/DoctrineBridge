<?php
namespace Metabor\Bridge\Doctrine\Event;

use Metabor\Bridge\Doctrine\KeyValue\Metadata;
use Metabor\Bridge\Doctrine\Observer\Subject;
use MetaborStd\Event\EventInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @author Oliver Tischlinger
 * 
 * @ORM\Entity
 *        
 */
class Event extends Subject implements EventInterface, \ArrayAccess
{
    const ENTITY_NAME = __CLASS__;
    
    use Metadata;

    /**
     * @var string
     *
     * @ORM\Column()
     * 
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     *
     */
    private $description;

    /**
     *
     * @var array
     */
    private $invokeArgs = array();

    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
        $this->name = $name;
        parent::__construct();
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
     *
     * @see MetaborStd\Event.EventInterface::getInvokeArgs()
     */
    public function getInvokeArgs()
    {
        return $this->invokeArgs;
    }

    /**
     * @see \MetaborStd\CallbackInterface::__invoke()
     */
    public function __invoke()
    {
        $this->invokeArgs = func_get_args();
        $this->notify();
        $this->invokeArgs = array();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
