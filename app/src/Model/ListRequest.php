<?php

namespace App\Model;

use App\Enum\RequestStatus;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Type;

class ListRequest
{
    #[Choice(callback: [RequestStatus::class, 'toArray'])]
    private ?string $status = null;

    #[Type(type: 'digit')]
    private ?string $from = null;

    #[Type(type: 'digit')]
    private ?string $to = null;

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(?string $from): static
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setTo(?string $to): static
    {
        $this->to = $to;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
}
