<?php
namespace Metabor\Doctrine\Statemachine;
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
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var string
     *
     * @ORM\Column(unique=true)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="State", mappedBy="process", indexBy="name")
     */
    private $states;

    /**
     * @var State
     * 
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(nullable=false)
     */
    private $initialState;

    /**
     * @param string $name
     * @param State $initialState
     */
    public function __construct($name = null, State $initialState = null)
    {
        $this->states = new ArrayCollection();
        $this->name = $name;
        $this->initialState = $initialState;
    }

    /**
     * @see \Metabor\Named::getName()
     */
    public function getName()
    {
        return $this->getName();
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
        $this->states->offsetSet($state->getName(), $state);
    }

    /**
     * @param State $state
     */
    public function removeState(State $state)
    {
        $this->states->removeElement($state);
    }

    /**
     * @see \MetaborStd\Statemachine\StateCollectionInterface::getState()
     */
    public function getState($name)
    {
        return $this->states->offsetGet($name);
    }

    /**
     * @see \MetaborStd\Statemachine\StateCollectionInterface::hasState()
     */
    public function hasState($name)
    {
        return $this->states->offsetExists($name);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

}
