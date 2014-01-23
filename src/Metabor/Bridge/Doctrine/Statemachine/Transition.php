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
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * 
     * @ORM\Column()
     * 
     */
    private $conditionName;

    /**
     * @var ConditionInterface
     *
     */
    private $condition;

    /**
     * @var string
     * 
     * @ORM\Column()
     * 
     */
    private $eventName;

    /**
     * @var State
     * 
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sourceState;

    /**
     * @var State
     * 
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(nullable=false)
     */
    private $targetState;

    /**
     * @var ExpressionLanguage
     */
    static private $expressionLanguage;

    /**
     * @param State $sourceState
     * @param State $targetState
     * @param string $eventName
     * @param string $condition
     */
    public function __construct(State $sourceState = null, State $targetState = null, $eventName = null, $conditionName = null)
    {
        $this->sourceState = $sourceState;
        $this->targetState = $targetState;
        $this->eventName = $eventName;
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
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @see \MetaborStd\Statemachine\TransitionInterface::getTargetState()
     */
    public function getTargetState()
    {
        return $this->getTargetState();
    }

    /**
     * @return Event
     */
    protected function getEvent()
    {
        if ($this->eventName) {
            return $this->sourceState->getEvent($this->getEventName());
        }
    }

    /**
     * @return \MetaborStd\Statemachine\ConditionInterface
     */
    protected function getCondition()
    {
        if (!$this->condition) {
            $this->condition = new SymfonyExpression($this->getConditionName(), self::$expressionLanguage);
        }
        return $this->condition;
    }

    /**
     * @see \MetaborStd\Statemachine\TransitionInterface::isActive()
     */
    public function isActive($subject, \ArrayAccess $context, EventInterface $event = null)
    {
        if ($this->getEvent() === $event) {
            if ($this->condition) {
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
}
