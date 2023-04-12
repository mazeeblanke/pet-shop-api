<?php

namespace App\Services\Auth;

use App\Services\Auth\Contracts\TokenManager;
use Illuminate\Http\Request;

class HeaderTokenManager implements TokenManager
{
    public const TOKEN_PREFIX = 'Bearer';

    private string $key = 'Authorization';

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getRequestToken(Request $request): ?string
    {
        $token = $request->headers->get($this->key);

        if (! $token) {
            return null;
        }

        if (
            ! \preg_match(
                '/' . self::TOKEN_PREFIX . '\s*(\S+)\b/i',
                $token,
                $matches
            )) {
            return null;
        }

        return $matches[1];
    }
}
