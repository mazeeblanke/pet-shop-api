<?php

namespace App\Models;

use App\Services\Filtering\Behaviors\HandleFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HandleFilters;

    protected $fillable = [
        'title',
        'uuid',
        'slug',
    ];
}
