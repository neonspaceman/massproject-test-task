<?php

namespace App\Model;

class UpdateRequestResponse
{
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }
}
