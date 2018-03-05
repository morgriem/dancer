<?php

namespace Morgriem\Dancer\Tests\Unit;

use Morgriem\Dancer\DancerException;
use Morgriem\Dancer\Dto;
use Morgriem\Dancer\Tests\Stubs\AnotherCustomDto;
use Morgriem\Dancer\Tests\Stubs\AnotherTestEntity;
use Morgriem\Dancer\Tests\Stubs\CustomDto;
use Morgriem\Dancer\Tests\Stubs\TestEntity;
use Morgriem\Dancer\Tests\Stubs\YetAnotherTestEntity;
use PHPUnit\Framework\TestCase;

class DtoTest extends TestCase
{
    /**
     * @dataProvider entityProvider
     * @param object $entity
     */
    public function testItGetsEntityProperties(object $entity)
    {
        $dto = new Dto($entity);

        $getFunctionalProperties = function(array $methods, string $propertyPrefix) {
            return array_map(function (string $method) use ($propertyPrefix) {
                return lcfirst(substr($method, strlen($propertyPrefix)));
            },array_filter($methods, function(string $method) use ($propertyPrefix) {
                return substr($method, 0, strlen($propertyPrefix)) === $propertyPrefix;
            }));
        };

        $methods = get_class_methods($entity);
        $getters = call_user_func($getFunctionalProperties, $methods, 'get');
        $issers = call_user_func($getFunctionalProperties, $methods, 'is');

        foreach (get_object_vars($entity) as $prop => $value) {
            $this->assertEquals($value, $dto->$prop);
        }

        foreach ($getters as $prop) {
            $this->assertEquals(call_user_func([$entity,'get' . ucfirst($prop)]),$dto->$prop);
        }

        foreach ($issers as $prop) {
            $this->assertEquals(call_user_func([$entity,'is' . ucfirst($prop)]),$dto->$prop);
        }
    }

    /**
     * @dataProvider computedProvider
     * @param AnotherTestEntity $entity
     */
    public function testItGetsComputedProperties(AnotherTestEntity $entity)
    {
        $dto = new CustomDto($entity);
        $data = $dto->toArray();

        foreach (array_filter(get_class_methods($dto), function (string $method) {
            return substr($method,0,3) === 'get' || substr($method,0,2) === 'is';
        }) as $method) {
            $prop = $method;
            if (substr($method,0,3) === 'get') {
                $prop = lcfirst(substr($method, 3));
            } else if (substr($method,0,2) === 'is') {
                $prop = lcfirst(substr($method, 2));
            }
            $this->assertEquals(call_user_func([$dto,$method]),$data[$prop]);
        }
    }

    /**
     * @dataProvider entityProvider
     * @param object $entity
     */
    public function testItRepresentsEntityAsArray(object $entity)
    {
        $dto = new Dto($entity);
        $data = $dto->toArray();
        $reflection = new \ReflectionClass($entity);
        foreach ($data as $key => $value) {
            $getterName = 'get' . ucfirst($key);
            $isserName = 'is' . ucfirst($key);
            if ($reflection->getProperty($key)->isPublic()) {
                $this->assertEquals($entity->$key, $value);
            } else if (method_exists($entity, $getterName)) {
                $this->assertEquals(call_user_func([$entity, $getterName]), $value);
            } else if (method_exists($entity, $isserName)) {
                $this->assertEquals(call_user_func([$entity, $isserName]), $value);
            }
        }
    }

    /**
     * @dataProvider computedProvider
     * @param object $entity
     */
    public function testItExcludesProperies(object $entity)
    {
        $dto = new CustomDto($entity);
        $data = $dto->toArray();
        foreach (CustomDto::excludedProperties() as $excludedProperty) {
            $this->assertFalse(array_key_exists($excludedProperty, $data));
        }
    }

    public function testItThrowsOnGetUndefinedProperty()
    {
        $this->expectException(DancerException::class);
        $this->expectExceptionCode(DancerException::PROPERTY_NOT_DEFINED);

        $dto = new Dto(new TestEntity(1));
        $undefined = $dto->undefinedProperty;
    }

    public function testItThrowsOnGetExcludedProperty()
    {
        $this->expectException(DancerException::class);
        $this->expectExceptionCode(DancerException::PROPERTY_EXCLUDED);

        $dto = new CustomDto(new AnotherTestEntity(1,new TestEntity(2)));
        $excluded = $dto->id;
    }

    public function testItThrowsOnWrongComputed()
    {
        $this->expectException(DancerException::class);
        $this->expectExceptionCode(DancerException::GENERIC_ERROR);

        $dto = new CustomDto(new TestEntity(1));
    }

    /**
     * @dataProvider precedenceProvider
     * @param YetAnotherTestEntity $entity
     */
    public function testDtoPropertyPrecedence(YetAnotherTestEntity $entity)
    {
        $originalId = $entity->id;
        $originalString = $entity->string;

        $entity->id = $entity->id + 2;
        $entity->string = $entity->string . 'x';

        $dto = new AnotherCustomDto($entity);
        $this->assertEquals($entity->id * 2, $dto->id);
        $this->assertEquals($entity->string . 'fff', $dto->string);
    }

    public function entityProvider()
    {
        return [
            [new TestEntity(1)],
            [new TestEntity(2,true)],
            [new AnotherTestEntity(1, new TestEntity(3))],
        ];
    }

    public function computedProvider()
    {
        return [
            [new AnotherTestEntity(1, new TestEntity(2))],
            [new AnotherTestEntity(2, new TestEntity(4))],
        ];
    }

    public function precedenceProvider()
    {
        return [
            [new YetAnotherTestEntity(3)],
            [new YetAnotherTestEntity(5,'abcz')],
        ];
    }
}

