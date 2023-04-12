<?php

namespace App\Services\Auth\Contracts;

use Illuminate\Contracts\Auth\Guard;

interface StatefulGuard extends Guard
{
    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool  $remember
     *
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false);

    // /**
    //  * TODO
    //  *
    //  * @param  array  $credentials
    //  * @return bool
    //  */
    // public function once(array $credentials = []);

    /**
     * Log a user into the application.
     *
     * @param  JWTAuthenticatable  $user
     * @param  bool  $remember
     *
     * @return void
     */
    public function login(JWTAuthenticatable $user, $remember = false);

    // /**
    //  * TODO.
    //  *
    //  * @param  mixed  $id
    //  * @param  bool  $remember
    //  * @return JWTAuthenticatable|bool
    //  */
    // public function loginUsingId($id, $remember = false);

    // /**
    //  * TODO.
    //  *
    //  * @param  mixed  $id
    //  * @return JWTAuthenticatable|bool
    //  */
    // public function onceUsingId($id);

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout();
}
