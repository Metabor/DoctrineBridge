<?php
namespace Metabor\Bridge\Doctrine\Statemachine;

use MetaborStd\Statemachine\Factory\StateNameDetectorInterface;
use MetaborStd\Statemachine\Factory\ProcessDetectorInterface;
use MetaborStd\Statemachine\ProcessInterface;

/**
 * @author Oliver Tischlinger
 */
class Detector implements ProcessDetectorInterface, StateNameDetectorInterface
{
    const ENTITY_NAME = __CLASS__;

    /**
     * @see \MetaborStd\Statemachine\Factory\ProcessDetectorInterface::detectProcess()
     */
    public function detectProcess(object $subject): ProcessInterface
    {
        if (!$subject instanceof StatefulEntity) {
            throw new \InvalidArgumentException('Subject has to be a StatefulEntity!');
        }

        return $subject->getProcess();
    }

    /**
     * @see \MetaborStd\Statemachine\Factory\StateNameDetectorInterface::detectCurrentStateName()
     */
    public function detectCurrentStateName(object $subject): ?string
    {
        if (!$subject instanceof StatefulEntity) {
            return null;
        }

        return $subject->getCurrentStateName();
    }
}
