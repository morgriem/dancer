<?php


namespace Morgriem\Dancer;


class Dto implements DtoInterface
{
    /**
     * @var object
     */
    private $entity;

    private $result = [];

    //region Public interface
    /**
     * Dto constructor.
     * @param object $entity
     */
    public function __construct(object $entity)
    {
        $this->entity = $entity;
        try {
            $this->result = $this->calculateResult($this->entity);
        } catch (\Throwable $e) {
            throw DancerException::genericError($e);
        }
    }

    final public function __get($name)
    {
        if (in_array($name, static::excludedProperties())) {
            throw DancerException::propertyExcluded($name);
        }
        if (array_key_exists($name, $this->result)) {
            return $this->result[$name];
        }
        throw DancerException::propertyNotDefined($name);
    }

    final public function toArray(): array
    {
        return $this->result;
    }

    /**
     * Returns reference to original entity.
     *
     * You must not neither leak this reference, nor mutate entity.
     *
     * @return object
     */
    final protected function originalEntity(): object
    {
        return $this->entity;
    }

    protected static function excludedProperties(): array
    {
        return [];
    }
    //endregion

    //region Implementation details
    /**
     * @param object $entity
     * @return array
     * @throws \ReflectionException
     */
    private function calculateResult(object $entity): array
    {
        $resultProps = array_merge(
            //entity public properties
            array_map(function(\ReflectionProperty $property) {
                return $property->getName();
            }, (new \ReflectionClass($entity))->getProperties(\ReflectionProperty::IS_PUBLIC)),

            //entity getters
            array_map(function(string $method) use ($entity) {
                return [$entity, $method];
            }, array_filter(get_class_methods($entity),  function(string $method) {
                return substr($method, 0, 3) === 'get' || substr($method, 0, 2) === 'is';
            })),

            //computed properties
            array_map(function(string $prop) {
                return [$this, $prop];
            }, $this->computedProperties())
        );

        $result = [];
        foreach ($resultProps as $prop) {
            if (is_callable($prop)) {
                $result[$this->getShortName($prop[1])] = call_user_func($prop);
            } else {
                $result[$prop] = $entity->$prop;
            }
        }

        $excludedProperties = array_flip(static::excludedProperties());
        return array_filter($result, function (string $key) use ($excludedProperties) {
            return false === array_key_exists($key, $excludedProperties);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function getShortName(string $property): string
    {
        if (substr($property,0, 3) === 'get') {
            return lcfirst(substr($property, 3));
        }
        else if (substr($property,0, 2) === 'is') {
            return lcfirst(substr($property, 2));
        }
        return $property;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    private function computedProperties(): array
    {
        return array_filter(
            array_map(function (\ReflectionMethod $method) {
                return $method->getShortName();
            }, (new \ReflectionClass($this))->getMethods(\ReflectionMethod::IS_PUBLIC)),
            function (string $method) {
                return substr($method, 0, 3) === 'get' || substr($method, 0, 2) === 'is';
            }
        );
    }
    //endregion

}