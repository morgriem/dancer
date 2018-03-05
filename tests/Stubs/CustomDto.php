<?php


namespace Morgriem\Dancer\Tests\Stubs;


use Morgriem\Dancer\Dto;

class CustomDto extends Dto
{
    public function __construct(object $entity)
    {
        parent::__construct($entity);
    }

    public function getSum()
    {
        /** @var AnotherTestEntity $entity*/
        $entity = $this->originalEntity();
        return $entity->getId() + $entity->getEntity()->getId();
    }

    public static function excludedProperties(): array {
        return [
            'id',
        ];
    }

}