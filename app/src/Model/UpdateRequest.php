<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints\NotBlank;

class UpdateRequest
{
    #[NotBlank]
    private string $comment;

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }
}
