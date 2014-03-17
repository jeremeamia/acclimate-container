<?php

namespace Acclimate\Container\Test\Adapter;

use Acclimate\Container\Adapter\ArrayAccessContainerAdapter;
use Acclimate\Container\ArrayContainer;

/**
 * @covers \Acclimate\Container\Adapter\ArrayAccessContainerAdapter
 */
class ArrayAccessContainerAdapterTest extends ContainerAdapterTestBase
{
    protected function createContainer()
    {
        $container = new ArrayContainer();
        $container['array_iterator'] = new \ArrayIterator(range(1, 5));
        $container['error'] = function() {
            throw new \RuntimeException;
        };

        return new ArrayAccessContainerAdapter($container);
    }

    public function testSupportsContainerInterface()
    {
        $container = $this->createContainer();

        $this->assertTrue($container->has('array_iterator'));
        $arrayIterator = $container->get('array_iterator');
        $this->assertEquals(array(1, 2, 3, 4, 5), iterator_to_array($arrayIterator));
    }

    public function testThrowsExceptionOnNonExistentItem()
    {
        $container = $this->createContainer();

        $this->assertFalse($container->has('foo'));

        $this->setExpectedException(self::NOT_FOUND_EXCEPTION);
        $container->get('foo');
    }

    public function testAdapterWrapsOtherExceptions()
    {
        $container = $this->createContainer();

        $this->setExpectedException(self::CONTAINER_EXCEPTION);
        $container->get('error');
    }

    public function testAdapterWrapsOtherExceptionsDuringGet()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');

        // This should trigger an error
        $container = new ArrayAccessContainerAdapter('not-a-container');
    }
}
