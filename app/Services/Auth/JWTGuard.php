<?php

namespace App\Services\Auth;

use App\Services\Auth\Contracts\EventFactory;
use App\Services\Auth\Contracts\StatefulGuard as ContractsStatefulGuard;
use App\Services\Auth\Contracts\TokenManager;
use App\Services\Auth\Exceptions\BeforeValid;
use App\Services\Auth\Exceptions\InvalidSignature;
use App\Services\Auth\Exceptions\InvalidToken;
use App\Services\Auth\Exceptions\NotPermitted;
use App\Services\Auth\Exceptions\TokenExpired;
use Exception;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validator;

class JWTGuard implements ContractsStatefulGuard
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
        $this->provider = $this->app->get('auth')
            ->createUserProvider($config['provider']);
    }

    public function user(): ?Authenticatable
    {
        if ($this->user) {
            return $this->user;
        }

        return $this->authenticateWithAccessToken(request());
    }

    public function validate(array $credentials = []): bool
    {
        if (! isset($this->user)) {
            return false;
        }

        return $this->validateCredentials($credentials, $this->user);
    }

    public function attempt(array $credentials = [], bool $remember = false): bool
    {
        $this->dispatchEvent(
            $this->eventFactory->createAttemptingEvent($this->name, $credentials, $remember)
        );

        $user = $this->provider->retrieveByCredentials($credentials);

        if (! ($user && $this->validateCredentials($credentials, $user))) {
            $this->dispatchEvent(
                $this->eventFactory->createFailedEvent($this->name, $user, $credentials)
            );

            return false;
        }

        $this->login($user, $remember);

        return true;
    }

    public function login(Authenticatable $user, bool $remember = false): void
    {
        $this->setUser($user);

        $accessToken = $this->issueAccessToken($user);

        $this->setUserAccessToken($accessToken);

        $this->dispatchEvent(
            $this
                ->eventFactory
                ->createLoginEvent($this->name, $user, $remember)
        );
    }

    public function logout(): void
    {
        $this->accessToken = null;

        if (isset($this->user)) {
            $this->dispatchEvent(
                $this->eventFactory->createLogoutEvent($this->name, $this->user)
            );
        }

        $this->user = null;
    }

    public function issueAccessToken(Authenticatable $user): Token
    {
        $issuedAt = new \DateTimeImmutable();

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

        return $this->builder->getToken(
            $this->app->get(Signer::class),
            $this->app->get(Key::class)
        );
    }

    /**
     * Set access token object
     */
    public function setUserAccessToken(Token $token): JWTGuard
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * Get a string representation of the access token
     */
    public function getAccessToken(): string|null
    {
        return Optional($this->accessToken)->toString();
    }

    private function validateCredentials(array $credentials, Authenticatable $user): bool
    {
        return $this
            ->provider
            ->validateCredentials($user, $credentials);
    }

    /**
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
    private function getValidToken(?string $accessToken): ?Token
    {
        if (! $accessToken) {
            return null;
        }

        try {
            $token = $this->parser->parse($accessToken);
        } catch (Exception $e) {
            throw new InvalidToken();
        }

        foreach ($this->validTokenRules($token) as $exceptionClass => $invalid) {
            if ($invalid) {
                throw new $exceptionClass();
            }
        }

        return $token;
    }

    /**
     * @return  array<class-string<\Throwable>, bool>
     */
    private function validTokenRules(Token $token)
    {
        return [
            TokenExpired::class => $token->isExpired(now()),
            BeforeValid::class => ! $token->hasBeenIssuedBefore(now()),
            NotPermitted::class => ! $this->validator
                ->validate($token, new PermittedFor(
                    config('jwt.permitted_for')
                )),
            InvalidSignature::class => ! $this->validator
                ->validate($token, new SignedWith(
                    $this->app->get(Signer::class),
                    $this->app->get(Key::class)
                )),
        ];
    }

    private function authenticateWithAccessToken(Request $request): ?Authenticatable
    {
        $accessToken = $this->getAccessTokenFromRequest($request);

        if (! $accessToken) {
            return null;
        }

        $user = $this->getUserByJWT($accessToken);

        if (! $user) {
            return null;
        }

        $this->setUserAccessToken($accessToken)->setUser($user);

        return $user;
    }

    private function getAccessTokenFromRequest(Request $request): Token|null
    {
        $accessToken = $this->tokenManager->getRequestToken($request);

        return $this->getValidToken($accessToken);
    }

    private function getUserByJWT(Token|UnencryptedToken $accessToken): ?Authenticatable
    {
        if (! $accessToken instanceof UnencryptedToken) {
            return null;
        }

        $uuid = $accessToken->claims()->get('sub');

        return $this->provider->retrieveById($uuid);
    }

    private function dispatchEvent(object $event): JWTGuard
    {
        if (isset($this->eventDispatcher)) {
            $this->eventDispatcher->dispatch($event);
        }

        return $this;
    }
}
