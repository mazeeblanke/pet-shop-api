<?php

namespace App\Services\Auth;

use App\Services\Auth\Contracts\EventFactory;
use App\Services\Auth\Contracts\JWTAuthenticatable;
use App\Services\Auth\Contracts\StatefulGuard;
use App\Services\Auth\Contracts\TokenManager;
use App\Services\Auth\Exceptions\BeforeValid;
use App\Services\Auth\Exceptions\InvalidSignature;
use App\Services\Auth\Exceptions\InvalidToken;
use App\Services\Auth\Exceptions\NotPermitted;
use App\Services\Auth\Exceptions\TokenExpired;
use Exception;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validator;

class JWTGuard implements StatefulGuard
{
    use GuardHelpers;

    protected Token|null $accessToken;

    protected Application $app;

    protected string $name;

    protected array $config;

    protected Builder $builder;

    protected TokenManager $tokenManager;

    protected Configuration $configuration;

    protected Parser $parser;

    protected Validator $validator;

    protected EventFactory $eventFactory;

    protected Dispatcher $eventDispatcher;

    public function __construct(string $name, Application $app, array $config)
    {
        $this->app = $app;
        $this->name = $name;
        $this->config = $config;
        $this->configuration = $this->app->get(Configuration::class);
        $this->builder = $this->configuration->builder();
        $this->parser = $this->configuration->parser();
        $this->validator = $this->configuration->validator();
        $this->tokenManager = $this->app->get(TokenManager::class);
        $this->eventFactory = $this->app->get(EventFactory::class);
        $this->eventDispatcher = $this->app->get(Dispatcher::class);
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        return $this->authenticateWithAccessToken(request());
    }

    public function validate(array $credentials = []): bool
    {
        return $this->validateCredentials($credentials, $this->user);
    }

    public function attempt(array $credentials = [], $remember = false): bool
    {
        $this->dispatchEvent(
            $this
                ->eventFactory
                ->createAttemptingEvent($this->name, $credentials, $remember)
        );

        $user = $this->provider->retrieveByCredentials($credentials);

        if (! ($user && $this->validateCredentials($credentials, $user))) {
            $this->dispatchEvent(
                $this
                    ->eventFactory
                    ->createFailedEvent($this->name, $user, $credentials)
            );

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

        $this->dispatchEvent(
            $this
                ->eventFactory
                ->createLoginEvent($this->name, $user, $remember)
        );
    }

    public function logout(): void
    {
        $this->user = null;
        $this->accessToken = null;

        $this->dispatchEvent(
            $this
                ->eventFactory
                ->createLogoutEvent($this->name, $this->user)
        );
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

    /**
     *
     * @param   string  $accessToken
     *
     * @throws InvalidToken
     *
     * @throws InvalidSignature
     *
     * @throws BeforeValid
     *
     * @throws TokenExpired
     *
     * @return  Token
     */
    private function getValidToken($accessToken): Token|null
    {
        if (! $accessToken) {
            return null;
        }

        try {
            $token = $this->parser->parse($accessToken);
        } catch (Exception $e) {
            throw new InvalidToken();
        }

        if (! $this->validator->validate($token, new SignedWith(
            $this->app->get(Signer::class),
            $this->app->get(Key::class)
        ))) {
            throw new InvalidSignature();
        }

        if (! $this->validator->validate($token, new PermittedFor(
            config('jwt.permitted_for')
        ))) {
            throw new NotPermitted();
        }

        if ($token->isExpired(now())) {
            throw new TokenExpired();
        }

        if (! $token->hasBeenIssuedBefore(now())) {
            throw new BeforeValid();
        }

        return $token;
    }

    private function authenticateWithAccessToken(
        Request $request
    ): JWTAuthenticatable|null {
        $accessToken = $this->getAccessTokenFromRequest($request);

        if (! $accessToken) {
            return null;
        }

        $user = $this->getUserByJWT($accessToken);

        if (! $user) {
            return null;
        }

        $this->setAccessToken($accessToken)->setUser($user);

        return $user;
    }

    private function getAccessTokenFromRequest(Request $request): Token|null
    {
        $accessToken = $this->tokenManager->getRequestToken($request);

        return $this->getValidToken($accessToken);
    }

    private function getUserByJWT($accessToken): JWTAuthenticatable
    {
        $uuid = $accessToken->claims()->get('sub');

        return $this->provider->retrieveById($uuid);
    }

    private function dispatchEvent($event): JWTGuard
    {
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch($event);
        }

        return $this;
    }
}
