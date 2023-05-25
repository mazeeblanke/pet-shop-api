<?php


return [
    'secret' => env('JWT_SECRET', 'test'),
    'isAsymmetric' => env('JWT_IS_ASYMMETRIC', true),
    'issuer' => env('JWT_ISSUER'),
    'permitted_for' => env('JWT_PERMITTED_FOR'),
    'signer' => env('JWT_SIGNER', '\Lcobucci\JWT\Signer\Hmac\Sha256'),
    'token_ttl' => env('JWT_TTL', 1000),
    'server-key' => env('JWT_SERVER_KEY', '/storage/app/private-key.pem'),
];
