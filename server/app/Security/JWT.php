<?php

namespace App\Security;

use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT as FirebaseJWT;
use stdClass;

class JWT
{
    private static string $algorithm = 'HS256';
    
    public static function create(string $data):string
    {
        $key = env('APP_KEY', '');
        $url = env('APP_URL', '');
        
        $payload = [
            'iss' => $url,
            'aud' => $url,
            'exp' => time() + 7200,
            'iat' => time(),
            'data' => $data
        ];

        return FirebaseJWT::encode($payload, $key, self::$algorithm);
    }

    public static function decoded(?string $token):?stdClass
    {
        try {
            if(!is_null($token)) {
                return FirebaseJWT::decode($token, new Key(env('APP_KEY', ''), self::$algorithm));
            }

            return null;
            
        } catch (\Throwable $th) {
            Log::error("Falha ao decodificar Token: ".$th->getMessage());
            return null;
        } 
    }

    public static function validate(string $token):bool
    {
        try {
            $key = env('APP_KEY', '');
            FirebaseJWT::decode($token, new Key($key, self::$algorithm));
            return true;
        } catch (\Throwable $th) {
            return false;
        } 
    }
}
