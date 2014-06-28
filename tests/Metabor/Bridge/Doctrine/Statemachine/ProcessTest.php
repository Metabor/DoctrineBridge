<?php
namespace Metabor\Bridge\Doctrine\Statemachine;

use MetaborStd\Statemachine\ProcessInterfaceTest;

/**
 * 
 * @author Oliver Tischlinger
 *
 */
class ProcessTest extends ProcessInterfaceTest
{
    /**
     * @see \MetaborStd\Statemachine\StateCollectionInterfaceTest::createTestInstance()
     */
    protected function createTestInstance()
    {
        $name = $this->getOneStateNameOfTheCollection();
        $state = new State($name);
        return new Process('TestProcess', $state);
    }

    /**
     * @see \MetaborStd\Statemachine\StateCollectionInterfaceTest::getOneStateNameOfTheCollection()
     */
    protected function getOneStateNameOfTheCollection()
    {
        return 'TestState';
    }
}
