<?php


namespace Morgriem\Dancer\Tests\Stubs;


final class AnotherTestEntity
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var TestEntity
     */
    private $entity;

    public $int = 5;

    public function __construct(string $id, TestEntity $entity)
    {
        $this->id = $id;
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return TestEntity
     */
    public function getEntity(): TestEntity
    {
        return $this->entity;
    }

}