<?php

namespace App\Contracts;

interface EmailServiceInterface {

	public function send(array $recipient, string $subject, string $htmlContent, string $textContent = '');
}