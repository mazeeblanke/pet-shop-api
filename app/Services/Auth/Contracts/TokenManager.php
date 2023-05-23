<?php

namespace App\Services\Auth\Contracts;

use Illuminate\Http\Request;

interface TokenManager
{
    public function setKey(string $key): self;

    public function getRequestToken(Request $request): ?string;
}
