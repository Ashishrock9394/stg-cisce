<?php

namespace App\Contracts;
use App\Models\User;

interface OtpChannelInterface {

	public function send(User $user, string $otp): bool;
}