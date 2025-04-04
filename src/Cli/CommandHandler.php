<?php

namespace SecureUpload\Cli;

use SecureUpload\Uploader\SecureUploader;

class CommandHandler
{
    public function handle(string $command, array $args): void
    {
        switch ($command) {
            case 'publish-env':
                $this->publishEnv();
                break;
            
            case 'test-upload':
                $this->testUpload($args);
                break;
                
            default:
                $this->showHelp();
                break;
        }
    }

    private function publishEnv(): void
    {
        $source = __DIR__ . '/../../.env.example';
        $target = getcwd() . '/.env';

        echo "\033[1;34mPublishing SecureUpload .env configuration...\033[0m\n";

        if (!file_exists($source)) {
            echo "\033[0;31mError: Source .env.example file not found in vendor package.\033[0m\n";
            return;
        }

        if (file_exists($target)) {
            echo "\033[0;33mWarning: .env already exists in your project root.\033[0m\n";
            echo "Do you want to overwrite it? (y/N): ";
            $handle = fopen("php://stdin", "r");
            $line = trim(fgets($handle));
            if (strtolower($line) !== 'y') {
                echo "Aborted.\n";
                exit;
            }
        }

        if (copy($source, $target)) {
            echo "\033[0;32m.env file successfully published to $target\033[0m\n";
        } else {
            echo "\033[0;31mFailed to copy .env file.\033[0m\n";
            exit(1);
        }
    }

    private function testUpload(array $args): void
    {
        echo "\033[1;34mTesting file upload...\033[0m\n";

        foreach ($args as $arg) {
            if (strpos($arg, '--file=') !== false) {
                $filePath = substr($arg, strlen('--file='));
                break;
            }
            else if (strpos($arg, '-f') !== false) {
                $filePath = substr($arg, strlen('-f'));
                break;
            }
            else {
                echo "Invalid argument: $arg\n";
            }
        }
        if (!isset($filePath)) {
            echo "Usage: php bin/secure-upload test-upload --file=PATH_TO_FILE\n";
            exit(1);
        }
        $fileName = basename($filePath);

        if (!file_exists($filePath)) {
            echo "❌ File not found: $filePath\n";
            exit(1);
        }

        $uploader = new SecureUploader();
        $result = $uploader->validate($filePath, $fileName);

        echo "🧪 Test Upload Result:\n";
        print_r($result);
    }


    private function showHelp(): void
    {
        echo "\nSecureUpload CLI\n";
        echo "Usage:\n";
        echo "  php bin/secure-upload publish-env              # Publish .env config file\n";
        echo "  php bin/secure-upload test-upload --file=PATH  # Test file validation\n";
        echo "  php binsecure-upload help                      # Show this message\n";
    }

    /**
     * Get options from the command line or web request
     * 
     * @param string $options
     * @param array $longopts
     * @return array
     */
    private function getoptreq ($options, $longopts)
    {
        if (PHP_SAPI === 'cli' || empty($_SERVER['REMOTE_ADDR']))
        {
            return getopt($options, $longopts);
        }
        else if (isset($_REQUEST))  // web script
        {
            $found = [];

            $shortopts = preg_split('@([a-z0-9][:]{0,2})@i', $options, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            $opts = array_merge($shortopts, $longopts);

            foreach ($opts as $opt)
            {
                if (substr($opt, -2) === '::')  // optional
                {
                    $key = substr($opt, 0, -2);

                    if (isset($_REQUEST[$key]) && !empty($_REQUEST[$key]))
                    $found[$key] = $_REQUEST[$key];
                    else if (isset($_REQUEST[$key]))
                    $found[$key] = false;
                }
                else if (substr($opt, -1) === ':')  // required value
                {
                    $key = substr($opt, 0, -1);

                    if (isset($_REQUEST[$key]) && !empty($_REQUEST[$key]))
                    $found[$key] = $_REQUEST[$key];
                }
                else if (ctype_alnum($opt))  // no value
                {
                    if (isset($_REQUEST[$opt]))
                    $found[$opt] = false;
                }
            }

            return $found;
        }

        return [];
    }
}
