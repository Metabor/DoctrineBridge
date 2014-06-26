<?php
namespace Metabor\Bridge\Doctrine\Event;

use Doctrine\ORM\Mapping as ORM;
use Metabor\Bridge\Doctrine\Observer\Subject;
use MetaborStd\Event\EventInterface;
use MetaborStd\MetadataInterface;

/**
 *
 * @author Oliver Tischlinger
 *
 * @ORM\Entity
 *
 */
class Event extends Subject implements EventInterface, \ArrayAccess, MetadataInterface
{
    const ENTITY_NAME = __CLASS__;

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
     *
     * @var array
     *
     * @ORM\Column( type="array" )
     */
    private $metadata = array();

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
    final public function __invoke()
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
