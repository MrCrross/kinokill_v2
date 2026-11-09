<?php

namespace App\Data\Pagination;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;

/**
 * Generic wrapper for a paginated admin list response, so every admin
 * Index page receives the same {data, currentPage, lastPage, ...} shape
 * regardless of the underlying resource.
 */
class PaginatedData extends Data
{
    /**
     * @param  array<int, mixed>  $data
     */
    public function __construct(
        public array $data,
        public int $currentPage,
        public int $lastPage,
        public int $perPage,
        public int $total,
    ) {}

    public static function fromPaginator(LengthAwarePaginator $paginator, Closure $map): self
    {
        return new self(
            data: $paginator->getCollection()->map($map)->values()->all(),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
        );
    }
}
