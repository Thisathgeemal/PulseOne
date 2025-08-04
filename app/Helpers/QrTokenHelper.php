<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class QrTokenHelper
{
    public static function generateDailyToken()
    {
        $date = now()->toDateString(); // like "2025-07-31"
        return Crypt::encryptString($date);
    }

    public static function validateToken($token)
    {
        try {
            $decryptedDate = Crypt::decryptString($token);
            return $decryptedDate === now()->toDateString();
        } catch (\Exception $e) {
            return false;
        }
    }
}
