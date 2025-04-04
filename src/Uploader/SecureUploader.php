<?php

namespace SecureUpload\Uploader;

use Exception;
use SecureUpload\Env\EnvLoader;
use SecureUpload\FileTypes\AllFileTypes;
use SecureUpload\Config\Config;

class SecureUploader
{
    private AllFileTypes $fileTypes;
    private array $lang;

    private Config $config;

    /**
     * SecureUploader constructor.
     *
     * @param array $allowedExtensions Allowed file extensions.
     * @param int $maxFileNameLength Maximum allowed file name length.
     * @param int $maxFileSize Maximum file size in KB.
     */
    public function __construct(array $allowedExtensions = []) {
        // Load environment variables
        $envPath = file_exists(getcwd() . '/.env') 
                    ? getcwd() . '/.env' 
                    : __DIR__ . '/../../.env';

        EnvLoader::load($envPath);

        $this->config = new Config();

        $langFile = __DIR__ . '/../../lang/lang-' . $this->config->language . '.php';

        $this->fileTypes = new AllFileTypes();
        $this->config->allowedExtensions = empty($allowedExtensions) ? $this->fileTypes->getAllExtensions() : $allowedExtensions;

        $this->lang = file_exists($langFile) ? include $langFile : [];
    }

    /**
     * Validate the uploaded file.
     *
     * @param string $filePath Temporary file path.
     * @param string $fileName Uploaded file name.
     * @return array Validation result.
     */
    public function validate(string $filePath, string $fileName): array
    {
        $startTime = microtime(true);
        try {
            if (!file_exists($filePath)) {
                throw new Exception("FileNotFound");
            }

            if (strlen($fileName) > $this->config->maxFileNameLength) {
                $this->lang['InvalidFileNameSize'] = sprintf($this->lang['InvalidFileNameSize'], $this->config->maxFileNameLength);
                throw new Exception('InvalidFileNameSize');
            }

            if (preg_match('/[\x00-\x1f\/:*?"<>|]/', $fileName)) {
                throw new Exception("InvalidCharInFileName");
            }

            $safeFileName = basename($fileName);
            $fileExtension = strtolower(pathinfo($safeFileName, PATHINFO_EXTENSION));
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $fileMimeType = $finfo->file($filePath);

            // Check file size in KB (using 1024 for conversion)
            if ((filesize($filePath) / 1024) > $this->config->maxFileSize) {
                $this->lang['FileSizeExceeded'] = sprintf($this->lang['FileSizeExceeded'], $this->config->maxFileSize);
                throw new Exception('FileSizeExceeded');
            }

            if (!in_array($fileExtension, $this->config->allowedExtensions, true)) {
                $this->lang['InvalidFileExtension'] = str_replace('#', implode(', ', $this->config->allowedExtensions), $this->lang['InvalidFileExtension']);
                throw new Exception('InvalidFileExtension');
            }

            // Special handling for xlsx files
            if (!($fileMimeType === 'application/octet-stream' && $fileExtension === 'xlsx')) {
                if ($fileMimeType !== $this->fileTypes->checkExtensionWithMime($fileExtension)) {
                    throw new Exception('InvalidFileType');
                }
            }

            $pattern = '/\b(?:SELECT|INSERT|UPDATE|DELETE|CREATE|ALTER|DROP|TRUNCATE|GRANT|REVOKE)\b|<script[^>]*>(.*?)<\/script>|<\?php(.*?)\?>/is';
            if (preg_match($pattern, file_get_contents($filePath))) {
                throw new Exception('InvalidContents');
            }

            if ($this->config->enableAntivirus) {
                $this->runAntivirusCheck($filePath);
            }

            $endTime = microtime(true);
            return ['time' => ($endTime - $startTime) * 1000, 'result' => "success"];
        } catch (Exception $e) {
            return ['error' => $this->lang[$e->getMessage()] ?? $e->getMessage()];
        }
    }

    /**
     * Run antivirus check using ClamAV.
     *
     * @param string $filePath
     * @throws Exception if antivirus check fails.
     */
    private function runAntivirusCheck(string $filePath): void
    {
        $clamavPath = $this->config->antivirusPath;
        if (!file_exists($clamavPath)) {
            throw new Exception("AntivirusFileNotFound");
        }

        $escapedFilePath = escapeshellarg($filePath);
        $pythonPath = $this->config->pythonPath;
        $pythonScript = __DIR__ . '../Scripts/python/scan_file.py';

        $command = sprintf('%s %s %s %s', $pythonPath, $pythonScript, $escapedFilePath, escapeshellarg($clamavPath));
        exec($command, $output, $returnVal);

        $out = [];
        foreach ($output as $item) {
            $decoded = json_decode($item, true);
            if (is_array($decoded)) {
                $out = $decoded;
                break;
            }
        }

        if ($returnVal === 0) {
            if (trim($out['result'] ?? '') !== "No threats found") {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                throw new Exception('InfectedFile');
            }
        } else {
            throw new Exception("ClamAVError: " . implode("\n", $output));
        }
    }

    public function getLang(): string
    {
        return $this->config->language;
    }
}
