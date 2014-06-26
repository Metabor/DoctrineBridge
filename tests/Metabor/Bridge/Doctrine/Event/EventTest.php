<?php
namespace Metabor\Bridge\Doctrine\Event;

use Metabor\Bridge\Doctrine\KeyValue\MetadataTest;
use MetaborStd\Event\EventInterfaceTest;

/**
 * 
 * @author Oliver Tischlinger
 *
 */
class EventTest extends EventInterfaceTest
{
	use MetadataTest;

    /**
     * @see \MetaborStd\NamedInterfaceTest::createTestInstance()
     */
    protected function createTestInstance()
    {
        return new Event('TestEvent');
    }

    /**
     *
     */
    public function testUsesMetadataForFlags()
    {
        $offset = 'TestOffset';
        $value = 'TestValue';
        $instance = $this->createTestInstance();
        $this->assertArrayNotHasKey($offset, $instance);
        $instance[$offset] = $value;
        $this->assertArrayHasKey($offset, $instance);
        $this->assertEquals($value, $instance[$offset]);
        unset($instance[$offset]);
        $this->assertArrayNotHasKey($offset, $instance);
    }
}
