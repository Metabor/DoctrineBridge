<?php
namespace Metabor\Bridge\Doctrine\KeyValue;

/**
 * 
 * @author Oliver Tischlinger
 *
 */
trait MetadataTest
{
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
