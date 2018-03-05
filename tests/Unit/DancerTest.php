<?php

namespace Morgriem\Dancer\Tests\Unit;

use Morgriem\Dancer\Dancer;
use Morgriem\Dancer\DancerException;
use Morgriem\Dancer\Dto;
use Morgriem\Dancer\Tests\Stubs\AnotherTestEntity;
use Morgriem\Dancer\Tests\Stubs\CustomDto;
use Morgriem\Dancer\Tests\Stubs\TestEntity;
use PHPUnit\Framework\TestCase;

class DancerTest extends TestCase
{
    /**
     * @dataProvider convertProvider
     * @param object $entity
     * @param string $dtoClass
     */
    public function testItConvertsToDtoOfGivenType(object $entity, string $dtoClass)
    {
        $this->assertInstanceOf($dtoClass, Dancer::buildDtoFor($entity, $dtoClass));
    }

    /**
     * @dataProvider notCompatibleTypeProvider
     * @param string $class
     */
    public function testItThrowsIfTypeIsNotCompatible(string $class)
    {
        $this->expectException(DancerException::class);
        $this->expectExceptionCode(DancerException::NOT_COMPATIBLE_TYPE);

        Dancer::buildDtoFor(new \stdClass(), $class);
    }

    public function convertProvider()
    {
        return [
            [new TestEntity(1), Dto::class],
            [new TestEntity(2, true), Dto::class],
            [new AnotherTestEntity(1, new TestEntity(3)), CustomDto::class],
        ];
    }

    public function notCompatibleTypeProvider()
    {
        return [
            [\DateTime::class],
            [\Exception::class],
        ];
    }
}
