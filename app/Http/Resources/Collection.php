<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class Collection extends ResourceCollection
{
    public function __construct($resource)
    {
        $this->additional = Arr::except($resource->toArray(), ['data']);

        $resource = $resource->getCollection();

        parent::__construct($resource);
    }
}
