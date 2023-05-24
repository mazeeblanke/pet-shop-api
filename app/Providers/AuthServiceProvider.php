<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Services\Auth\Contracts\EventFactory as ContractsEventFactory;
use App\Services\Auth\Contracts\TokenManager;
use App\Services\Auth\Events\EventFactory;
use App\Services\Auth\HeaderTokenManager;
use App\Services\Auth\JWTGuard;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->bootJWTAuth();
    }

    public function register(): self
    {
        $this->registerJWTAuth();

        return $this;
    }

    private function bootJWTAuth(): void
    {
        $auth = $this->app->get('auth');
        $auth->extend(
            'jwt',
            fn ($app, $name, array $config) => new JWTGuard(
                $name,
                $app,
                $config
            )
        );
    }

    private function registerJWTAuth(): void
    {
        $this
            ->registerJWTGuard()
            ->registerTokenManager()
            ->registerEventFactory()
            ->registerSigner()
            ->registerSecretKey()
            ->registerConfiguration();
    }

    private function registerJWTGuard(): self
    {
        $this->app->singleton(Guard::class, fn () => $this->app->get('auth')->guard());

        return $this;
    }

    private function registerEventFactory(): self
    {
        $this->app->singleton(ContractsEventFactory::class, EventFactory::class);

        return $this;
    }

    private function registerTokenManager(): self
    {
        $this->app->singleton(TokenManager::class, HeaderTokenManager::class);

        return $this;
    }

    private function registerSigner(): self
    {
        $this->app->singleton(Signer::class, function () {
            $signerClass = config('jwt.signer');

            return new $signerClass();
        });

        return $this;
    }

    private function registerSecretKey(): self
    {
        $this->app->singleton(Key::class, fn () => InMemory::plainText('my-key-as-plaintextmy-key-as-plaintext')); // use config to set the key

        return $this;
    }

    private function registerConfiguration(): self
    {
        if (config('jwt.isAsymmetric')) {
            $this->app->singleton(
                Configuration::class,
                fn () => Configuration::forAsymmetricSigner(
                    $this->app->get(Signer::class),
                    InMemory::file(base_path(config('jwt.server-key'))),
                    $this->app->get(Key::class)
                )
            );
        } else {
            $this->app->singleton(
                Configuration::class,
                fn () => Configuration::forSymmetricSigner(
                    $this->app->get(Signer::class),
                    $this->app->get(Key::class)
                )
            );
        }

        return $this;
    }
}
