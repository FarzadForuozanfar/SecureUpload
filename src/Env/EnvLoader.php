<?php 

namespace SecureUpload\Env;

class EnvLoader
{
    public static function load(): void
    {
        $envPath = __DIR__ . '/../../.env'; 
        if (file_exists( $envPath )) {
            $env = parse_ini_file($envPath);
            foreach ($env as $key => $value) {
                putenv("$key=$value");
            }
        }
        else {
            die("Error: .env file not found in the root directory.");
        }
    }
}