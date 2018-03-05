<?php

namespace Morgriem\Dancer;

interface DtoInterface
{
    public function __get($name);

    public function toArray(): array;
}