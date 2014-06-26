<?php
namespace Metabor\Bridge\Doctrine\Statemachine;

use MetaborStd\Statemachine\Factory\StateNameDetectorInterface;
use MetaborStd\Statemachine\Factory\ProcessDetectorInterface;

/**
 *
 * @author Oliver Tischlinger
 *
 */
class Detector implements ProcessDetectorInterface, StateNameDetectorInterface
{
    const ENTITY_NAME = __CLASS__;

    /**
     * @see \MetaborStd\Statemachine\Factory\ProcessDetectorInterface::detectProcess()
     */
    public function detectProcess($subject)
    {
        if ($subject instanceof StatefulEntity) {
            return $subject->getProcess();
        }
    }

    /**
     * @see \MetaborStd\Statemachine\Factory\StateNameDetectorInterface::detectCurrentStateName()
     */
    public function detectCurrentStateName($subject)
    {
        if ($subject instanceof StatefulEntity) {
            return $subject->getCurrentStateName();
        }
    }

}
