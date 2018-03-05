<?php


namespace Morgriem\Dancer\Tests\Stubs;


final class TestEntity
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var bool
     */
    private $bool;
    /**
     * @var \DateTimeImmutable
     */
    private $date;

    public function __construct(string $id, bool $bool = false, \DateTimeImmutable $date = null)
    {
        $this->id = $id;
        $this->bool = $bool;
        $this->date = $date ?? new \DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isBool(): bool
    {
        return $this->bool;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }


}