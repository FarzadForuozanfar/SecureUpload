<?php 

namespace SecureUpload\Env;

class EnvLoader
{
    public static function load(string $envPath = ''): void
    {
        $envPath = empty($envPath) ? __DIR__ . '/../../.env' : $envPath; 
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