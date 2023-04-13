<?php

namespace App\Services\Filtering;

use Illuminate\Contracts\Database\Eloquent\Builder;

class UserFilter extends BaseFilter
{
    /**
     * Specific filters.
     *
     */
    protected array $filters = [
        'email'
    ];

    /**
     * Filter email.
     */
    protected function email(): Builder
    {
        return $this
            ->builder
            ->whereEmail(
                $this->request->email
            );
    }
}
