<?php
namespace Metabor\Bridge\Doctrine\Event;

use Metabor\Bridge\Doctrine\KeyValue\Metadata;
use Metabor\Observer\Subject;
use MetaborStd\Event\EventInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @author Oliver Tischlinger
 * 
 * @ORM\Table()
 * @ORM\Entity
 *        
 */
class Event extends Metadata implements EventInterface
{
    const ENTITY_NAME = __CLASS__;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * 
     */
    private $id;

    /**
     *
     * @var SplObjectStorage
     * 
     * @ORM\Column(type="object")
     * 
     */
    private $observers;

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
        $this->observers = new \SplObjectStorage();
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @see SplSubject::attach()
     */
    public function attach(SplObserver $observer)
    {
        $this->observers->attach($observer);
    }

    /**
     *
     * @see SplSubject::detach()
     */
    public function detach(SplObserver $observer)
    {
        $this->observers->detach($observer);
    }

    /**
     *
     * @see SplSubject::notify()
     */
    public function notify()
    {
        /* @var $observer SplObserver */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * @return \Traversable
     */
    public function getObservers()
    {
        return $this->observers;
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

}
