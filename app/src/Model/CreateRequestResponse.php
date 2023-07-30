<?php

namespace App\Model;

use OpenApi\Attributes\Property;

class CreateRequestResponse
{
    private int $id;

    #[Property(title: 'Created request Id')]
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
