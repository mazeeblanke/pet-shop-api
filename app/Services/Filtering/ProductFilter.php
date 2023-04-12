<?php

namespace App\Services\Filtering;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductFilter extends BaseFilter
{
    /**
     * Specific filters.
     *
     * @var array
     */
    protected $filters = [
        'title',
    ];

    /**
     * Filter title.
     *
     * @return Builder
     */
    protected function title(): Builder
    {
        return $this
            ->builder
            ->whereTitle(
                $this->request->title
            );
    }
}
