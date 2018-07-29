<?php

namespace Atnic\EmailVerification\Traits;

use Atnic\EmailVerification\Exceptions\VerificationFailedException;

use Illuminate\Support\Facades\Validator;

/**
 * Email Verifiable Traits
 */
trait EmailVerifiable
{
    /**
     * Is Email Verification Timeout Expired
     * @return bool
     */
    public function isEmailVerificationTimeoutExpired()
    {
        return !$this->isEmailVerified() && $this->{$this->getCreatedAtColumn()}->addMinutes(isset($this->emailVerificationTimeout) ? $this->emailVerificationTimeout : 0)->isPast();
    }

    /**
     * Is Email Verified
     * @return bool
     */
    public function isEmailVerified()
    {
        return $this->{$this->getEmailVerifiedColumn()};
    }

    /**
     * Get Email Verified Column
     * @return string
     */
    public function getEmailVerifiedColumn()
    {
        return $this->emailVerifiedColumn ? : 'email_verified';
    }

    /**
     * Generate Email Verification URL
     * @return string
     */
    public function generateEmailVerificationUrl()
    {
        return route('verify_email', [ encrypt((object) [
            'email' => $this->email,
            'expired' => now()->addMinutes(isset($this->emailVerificationExpired) ? $this->emailVerificationExpired : 60)->toDateTimeString()
        ]) ]);
    }
}
