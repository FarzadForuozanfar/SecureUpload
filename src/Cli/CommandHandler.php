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
                
            case 'scan-file':
                $this->scanFile($args);
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


    /**
     * Get options from the command line or web request
     * 
     * @param string $options
     * @param array $longopts
     * @return array
     */
    private function scanFile(array $args): void
    {
        echo "\033[1;34mScanning file...\033[0m\n";

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
            echo "Usage: php bin/secure-upload scan-file --file=PATH_TO_FILE\n";
            exit(1);
        }
        if (!file_exists($filePath)) {
            echo "❌ File not found: $filePath\n";
            exit(1);
        }

        $config = new \SecureUpload\Config\Config();
        $clamavPath = $config->getConfig('antivirusPath');
        $pythonPath = $config->getConfig('pythonPath');
        $pythonScript = __DIR__ . '/../Scripts/python/scan_file.py';
        
        $command = escapeshellcmd("$pythonPath $pythonScript " . escapeshellarg($filePath) . ' ' . escapeshellarg($clamavPath));
        
        exec($command, $output, $returnCode);
        
        $result = null;
        foreach ($output as $line) {
            $decoded = json_decode($line, true);
            if (is_array($decoded)) {
                $result = $decoded;
                break;
            }
        }
        
        echo "🔍 Scan Result:\n";
        if ($returnCode === 0 && isset($result['result'])) {
            echo $result['result'] === "No threats found"
                ? "✅ No threats found\n"
                : "❌ Infected File: " . $result['result'] . "\n";
        } else {
            echo "⚠️ Scan failed or unexpected output:\n";
            print_r($output);
        }
    }


    private function showHelp(): void
    {
        echo "\nSecureUpload CLI\n";
        echo "Usage:\n\n";
        echo "  php bin/secure-upload publish-env              # Publish .env config file\n";
        echo "  php bin/secure-upload test-upload --file=PATH  # Test file validation\n";
        echo "  php bin/secure-upload scan-file --file=PATH    # Run ClamAV scan on file\n";
        echo "  php binsecure-upload help                      # Show this message\n\n";
    }
}
