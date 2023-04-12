<?php

namespace App\Services\Auth\Events;

use App\Services\Auth\Contracts\EventFactory as ContractsEventFactory;
use App\Services\Auth\Contracts\JWTAuthenticatable;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class EventFactory implements ContractsEventFactory
{
    public function createAuthenticated(
        string $guardName,
        JWTAuthenticatable $user
    ): Authenticated {
        return new Authenticated($guardName, $user);
    }

    public function createAttemptingEvent(
        string $guardName,
        array $credentials,
        bool $remember = false
    ): Attempting {
        return new Attempting($guardName, $credentials, $remember);
    }

    public function createLogoutEvent(string $guardName, $user): Logout
    {
        return new Logout($guardName, $user);
    }

    public function createLoginEvent(
        string $guardName,
        JWTAuthenticatable $user,
        bool $remember = false
    ): Login {
        return new Login($guardName, $user, $remember);
    }

    public function createFailedEvent(
        string $guardName,
        ?JWTAuthenticatable $user,
        array $credentials
    ): Failed {
        return new Failed($guardName, $user, $credentials);
    }
}
