# Laravel Email Verification

## Implementation

Laravel Email Verification is a package for generate scaffolding code for email verification within just few step.

But before that make sure you already config you `.env` especially for `MAIL_*` keys. I prefer you to set up using [mailtrap.io](http://mailtrap.io), its super easy!

Okay, then this is the step to install Laravel Email Verification:

1. Install this package
```bash
composer require atnic/laravel-email-verification
```

- Make sure you're already run `php artisan make:auth` before, then run
```bash
php artisan make:email-verification
```

- Run migration to add `email_verified` column to users table
```bash
php artisan migrate
```

- Add `Atnic\EmailVerification\Traits\EmailVerifiable` trait to `User` model.
```php
<?php
...
use Atnic\EmailVerification\Traits\EmailVerifiable;
class User extends Authenticatable
{
    use EmailVerifiable;
    ...
```

- In `app/Http/Kernel.php`, modify `$routeMiddleware` property, change `auth` middleware
```php
<?php
...
class Kernel extends HttpKernel
{
    ...
    protected $routeMiddleware = [
        // 'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth' => \Atnic\EmailVerification\Http\Middleware\Authenticate::class,
        ...
```

- Override `registered()` method on `RegisterController`
```php
<?php
...
use Atnic\EmailVerification\Notifications\EmailVerification;
class RegisterController extends Controller
{
    ...
    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered($request, $user)
    {
        $user->notify(new EmailVerification($user->generateEmailVerificationUrl()));
        if ($user->isEmailVerificationTimeoutExpired()) {
            auth()->logout();
            return response()->redirectToRoute('verify_email.resend', [ 'email' => $user->email ])->with('status', __('email-verification::verify_email.link_sent'));
        }
    }
    ...
```

- Done!

## Security Vulnerabilities

If you discover a security vulnerability within Laravel Email Verification, please send an e-mail to Farid Inawan via [frdteknikelektro@gmail.com](mailto:frdteknikelektro@gmail.com). All security vulnerabilities will be promptly addressed.

## License

Laravel Email Verification is open-sourced package licensed under the [MIT license](https://opensource.org/licenses/MIT).
