<?php

namespace App\Models;

use App\Services\Filtering\Behaviors\HandleFilters;
use App\Services\Filtering\Contracts\Filter;
use App\Services\Filtering\Contracts\Filterable;
use Database\Factories\BrandFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Brand
 *
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static BrandFactory factory($count = null, $state = [])
 * @method static Builder|Brand filter(Filter $filters)
 * @method static Builder|Brand newModelQuery()
 * @method static Builder|Brand newQuery()
 * @method static Builder|Brand query()
 * @method static Builder|Brand whereCreatedAt($value)
 * @method static Builder|Brand whereId($value)
 * @method static Builder|Brand whereSlug($value)
 * @method static Builder|Brand whereTitle($value)
 * @method static Builder|Brand whereUpdatedAt($value)
 * @method static Builder|Brand whereUuid($value)
 *
 * @mixin \Eloquent
 */
class Brand extends Model implements Filterable
{
    use HasFactory;
    use HandleFilters;

    protected $fillable = [
        'title',
        'uuid',
        'slug',
    ];
}
