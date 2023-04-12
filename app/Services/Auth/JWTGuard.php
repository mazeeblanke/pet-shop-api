<?php

namespace App\Services\Auth;

use App\Services\Auth\Contracts\JWTAuthenticatable;
use App\Services\Auth\Contracts\StatefulGuard;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Foundation\Application;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;

class JWTGuard implements StatefulGuard
{
    use GuardHelpers;

    protected Token|null $accessToken;

    protected Application $app;

    protected string $name;

    protected array $config;

    protected Builder $builder;

    protected Configuration $configuration;

    public function __construct(string $name, Application $app, array $config)
    {
        $this->app = $app;
        $this->name = $name;
        $this->config = $config;
        $this->configuration = $this->app->get(Configuration::class);
        $this->builder = $this->configuration->builder();
    }

    public function validate(array $credentials = []): bool
    {
        return $this->validateCredentials($credentials, $this->user);
    }

    public function attempt(array $credentials = [], $remember = false): bool
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if (! ($user && $this->validateCredentials($credentials, $user))) {
            return false;
        }

        $this->login($user, $remember);

        return true;
    }

    public function login(JWTAuthenticatable $user, $remember = false): void
    {
        $this->setUser($user);

        $accessToken = $this->issueAccessToken($user);

        $this->setAccessToken($accessToken);
    }

    public function issueAccessToken(JWTAuthenticatable $user): Token
    {
        $issuedAt = new \DateTimeImmutable();
        $claims = $user->getClaims();

        $this->builder = $this->builder
            ->issuedBy(config('jwt.issuer'))
            ->permittedFor(config('jwt.permitted_for'))
            ->relatedTo($user->getAuthIdentifier())
            ->issuedAt($issuedAt);

        $ttl = config('jwt.token_ttl');
        if ($ttl) {
            $expiresAt = $issuedAt->add(new \DateInterval('PT' . $ttl . 'M'));
            $this->builder = $this->builder->expiresAt($expiresAt);
        }

        foreach ($claims as $name => $value) {
            $this->builder = $this->builder->withClaim($name, $value);
        }

        $accessToken = $this->builder->getToken(
            $this->app->get(Signer::class),
            $this->app->get(Key::class)
        );

        return $accessToken;
    }

    /**
     * Set access token object
     *
     */
    public function setAccessToken(Token $token): JWTGuard
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * Get a string representation of the access token
     *
     */
    public function getAccessToken(): string|null
    {
        return Optional($this->accessToken)->toString();
    }

    private function validateCredentials(
        array $credentials,
        JWTAuthenticatable $user
    ): bool {
        return $this
            ->provider
            ->validateCredentials($user, $credentials);
    }
}
