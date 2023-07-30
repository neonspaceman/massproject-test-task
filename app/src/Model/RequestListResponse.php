<?php

namespace App\Model;

class RequestListResponse
{
    /**
     * @var array<RequestListItem>
     */
    private array $items;

    /**
     * @return array<RequestListItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array<RequestListItem> $items
     * @return $this
     */
    public function setItems(array $items): static
    {
        $this->items = $items;
        return $this;
    }
}
