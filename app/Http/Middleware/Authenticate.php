<?php

namespace Atnic\EmailVerification\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Base;
use Atnic\EmailVerification\Traits\EmailVerifiable;

/**
 * Authenticate Middleware
 */
class Authenticate extends Base
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($guards);
        $user = $request->user();
        if (in_array(EmailVerifiable::class, class_uses(get_class($user))) && $user->isEmailVerificationTimeoutExpired()) {
            if (method_exists(auth()->guard(), 'logout')) auth()->logout();
            return $request->expectsJson()
                        ? response()->json(['message' => 'Email is not verified'], 401)
                        : redirect()->guest(route('verify_email.resend', [ 'email' => $user->email ]));
        }

        return $next($request);
    }
}
