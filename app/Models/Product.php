<?php

namespace App\Models;

use App\Services\Filtering\Behaviors\HandleFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HandleFilters;

    protected $casts = [
        'metadata' => 'array',
    ];
}
