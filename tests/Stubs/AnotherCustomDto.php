<?php


namespace Morgriem\Dancer\Tests\Stubs;


use Morgriem\Dancer\Dto;

class AnotherCustomDto extends Dto
{
    public function __construct(YetAnotherTestEntity $entity)
    {
        parent::__construct($entity);
    }

    public function getId() {
        return $this->originalEntity()->id * 2;
    }

}