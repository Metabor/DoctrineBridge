<?php
namespace Metabor\Bridge\Doctrine\KeyValue;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @author Oliver Tischlinger
 * 
 *        
 */
trait Metadata
{
    /**
     *
     * @var array
     * 
     * @ORM\Column( type="array" )
     */
    protected $metadata = array();

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
        $this->metadata[$offset];
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
