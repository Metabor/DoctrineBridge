<?php
namespace Metabor\Bridge\Doctrine\Statemachine;

use MetaborStd\Event\EventInterface;
use Metabor\Statemachine\Condition\SymfonyExpression;
use MetaborStd\Statemachine\ConditionInterface;
use Metabor\Bridge\Doctrine\Event\Event;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\Expression;
use MetaborStd\Statemachine\TransitionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @author Oliver Tischlinger
 * 
 * @ORM\Table()
 * @ORM\Entity
 *        
 */
class Transition implements TransitionInterface
{
    const ENTITY_NAME = __CLASS__;

    /**
     * @var ExpressionLanguage
     */
    static private $expressionLanguage;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var State
     * 
     * @ORM\ManyToOne(targetEntity="State", inversedBy="transitions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $sourceState;

    /**
     * @var State
     * 
     * @ORM\ManyToOne(targetEntity="State", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $targetState;

    /**
     * @var \Metabor\Bridge\Doctrine\Event\Event
     * 
     * @ORM\ManyToOne(targetEntity="Metabor\Bridge\Doctrine\Event\Event", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * 
     */
    private $event;

    /**
     * @var string
     * 
     * @ORM\Column(nullable=true)
     * 
     */
    private $conditionName;

    /**
     * @var ConditionInterface
     *
     */
    private $condition;

    /**
     * @var float
     *
     * @ORM\Column(type="float", options={"default" = 1})
     *
     */
    private $weight = 1;

    /**
     * @param State $sourceState
     * @param State $targetState
     * @param string $eventName
     * @param string $condition
     */
    public function __construct(State $sourceState = null, State $targetState = null, Event $event = null, $conditionName = null)
    {
        $this->sourceState = $sourceState;
        $this->targetState = $targetState;
        $this->event = $event;
        $this->conditionName = $conditionName;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getConditionName()
    {
        return $this->conditionName;
    }

    /**
     * @see \MetaborStd\Statemachine\TransitionInterface::getTargetState()
     */
    public function getTargetState()
    {
        return $this->targetState;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $event = null)
    {
        if ($event) {
            $this->getSourceState()->getEvents()->add($event);
        }
        $this->event = $event;
    }

    /**
     * @see \MetaborStd\Statemachine\TransitionInterface::getEventName()
     */
    public function getEventName()
    {
        if ($this->event) {
            return $this->event->getName();
        }
    }

    /**
     * @return \MetaborStd\Statemachine\ConditionInterface
     */
    protected function getCondition()
    {
        if (!$this->condition) {
            $values = array();
            $values['sourceState'] = $this->sourceState;
            $values['targetState'] = $this->targetState;
            $this->condition = new SymfonyExpression($this->conditionName, $values, self::$expressionLanguage);
        }
        return $this->condition;
    }

    /**
     * @see \MetaborStd\Statemachine\TransitionInterface::isActive()
     */
    public function isActive($subject, \ArrayAccess $context, EventInterface $event = null)
    {
        if ($this->event === $event) {
            if ($this->conditionName) {
                return $this->getCondition()->checkCondition($subject, $context);
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @link http://symfony.com/doc/current/components/expression_language/index.html
     * @param string $conditionName
     */
    public function setConditionName($conditionName)
    {
        $this->conditionName = $conditionName;
    }

    /**
     * @param ExpressionLanguage $expressionLanguage
     */
    static public function setExpressionLanguage(ExpressionLanguage $expressionLanguage = null)
    {
        self::$expressionLanguage = $expressionLanguage;
    }

    /**
     * @return \Metabor\Bridge\Doctrine\Statemachine\State
     */
    public function getSourceState()
    {
        return $this->sourceState;
    }

    /**
     * @param State $sourceState
     */
    public function setSourceState(State $sourceState)
    {
        $this->sourceState = $sourceState;
    }

    /**
     * @param State $targetState
     */
    public function setTargetState(State $targetState)
    {
        $this->targetState = $targetState;
    }

    /**
     * @param string $eventName
     */
    public function setEventName($eventName)
    {
        $this->event = $this->sourceState->findOrCreateEvent($eventName);
    }

}
