<?php
namespace Metabor\Bridge\Doctrine\Statemachine;

use Metabor\Bridge\Doctrine\Observer\Observer;
use MetaborStd\Event\EventInterface;

/**
 *
 * @author Oliver Tischlinger
 *
 */
abstract class Command extends Observer
{
    const ENTITY_NAME = __CLASS__;

    /**
     *
     * @param  \SplSubject               $subject
     * @throws \InvalidArgumentException
     */
    public function update(\SplSubject $subject)
    {
        if (!$subject instanceof EventInterface) {
            throw new \InvalidArgumentException('Command can only be attached to an event!');
        }
        call_user_func_array($this, $subject->getInvokeArgs());
    }

    /**
     * Overwrite this to change the name for the Command that is displayed in the graph
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }
}
