<?php

namespace App\Services\Auth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;

interface StatefulGuard extends Guard
{
    /**
     * Attempt to authenticate a user using the given credentials.
     */
    public function attempt(array $credentials = [], bool $remember = false): bool;

    // /**
    //  *
    //  * @param  array  $credentials
    //  * @return bool
    //  */
    // public function once(array $credentials = []);

    /**
     * Log a user into the application.
     */
    public function login(Authenticatable $user, bool $remember = false): void;

    // /**
    //  * @param  mixed  $id
    //  * @param  bool  $remember
    //  * @return Authenticatable|bool
    //  */
    // public function loginUsingId($id, $remember = false);

    // /**
    //  *
    //  * @param  mixed  $id
    //  * @return Authenticatable|bool
    //  */
    // public function onceUsingId($id);

    /**
     * Log the user out of the application.
     */
    public function logout(): void;
}
