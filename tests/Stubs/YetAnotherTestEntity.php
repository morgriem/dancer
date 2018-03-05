<?php


namespace Morgriem\Dancer\Tests\Stubs;


class YetAnotherTestEntity
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $string;

    public function __construct(int $id, string $string = '')
    {
        $this->id = $id;
        $this->string = $string;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string . 'fff';
    }

}