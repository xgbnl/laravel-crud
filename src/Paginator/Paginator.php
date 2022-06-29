<?php

declare(strict_types=1);

namespace Xgbnl\Business\Paginator;

use Illuminate\Pagination\LengthAwarePaginator;

class Paginator extends LengthAwarePaginator
{
    public function toArray(): array
    {
        return [
            'pageNum'  => $this->currentPage(),
            'pageSize' => $this->perPage(),
            'total'    => $this->total(),
            'next'     => $this->nextPageUrl(),
            'prev'     => $this->previousPageUrl(),
            'list'     => array_values($this->items->toArray())
        ];
    }
}
