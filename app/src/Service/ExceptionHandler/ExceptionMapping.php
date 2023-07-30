<?php

namespace App\Service\ExceptionHandler;

class ExceptionMapping
{
    public function __construct(
        private int $code,
        private bool $hidden)
    {
    }

    public static function fromCode(int $code, $hidden = true): static
    {
        return new static($code, $hidden);
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }
}
