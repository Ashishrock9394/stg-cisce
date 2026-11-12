<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class StudentEncryptionHelper
{
    /**
     * Encrypt value
     */
    public static function encryptValue($value)
    {
        try {

            if (
                $value === null ||
                $value === ''
            ) {
                return null;
            }

            return Crypt::encryptString((string) $value);

        } catch (\Exception $e) {

            \Log::error('Encryption Failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Decrypt value
     */
    public static function decryptValue($value)
    {
        try {

            if (
                $value === null ||
                $value === ''
            ) {
                return null;
            }

            return Crypt::decryptString($value);

        } catch (DecryptException $e) {

            \Log::error('Decryption Failed: ' . $e->getMessage());

            return null;
        }
    }
}