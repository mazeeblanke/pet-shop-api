<?php

namespace App\Models;

use App\Services\Filtering\Behaviors\HandleFilters;
use App\Services\Filtering\Contracts\Filter;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static CategoryFactory factory($count = null, $state = [])
 * @method static Builder|Category filter(Filter $filters)
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereSlug($value)
 * @method static Builder|Category whereTitle($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @method static Builder|Category whereUuid($value)
 *
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory, HandleFilters;

    protected $fillable = [
        'title',
        'uuid',
        'slug',
    ];
}
