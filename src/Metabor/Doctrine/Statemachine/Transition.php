<?php
namespace Metabor\Doctrine\Statemachine;
use Metabor\Doctrine\Event\Event;

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
     */
    private $sourceState;

    /**
     * @var State
     */
    private $targetState;

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @var Expression
     */
    private $expression;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Symfony\Component\ExpressionLanguage\ExpressionLanguage
     */
    protected function getExpressionLanguage()
    {
        return $this->expressionLanguage;
    }

    /**
     * @return string
     */
    public function getConditionName()
    {
        return $this->condition;
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

    public function isActive($subject, ArrayAccess $context, EventInterface $event = null)
    {
        if ($this->getEvent() === $event) {
            $language = $this->getExpressionLanguage();

        }
    }

    /**
     * @link http://symfony.com/doc/current/components/expression_language/index.html
     * @param string $condition
     */
    public function setConditionName($condition)
    {
        $this->condition = $condition;
    }

}
