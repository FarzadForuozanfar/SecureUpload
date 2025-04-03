<?php

namespace SecureUpload\Uploader;

use Exception;
use SecureUpload\Env\EnvLoader;
use SecureUpload\FileTypes\AllFileTypes;


class SecureUploader
{
    private array $allowedExtensions;
    private int $maxFileNameLength;
    private int $maxFileSize;
    private bool $antivirusEnabled;
    private bool $enableLogging;
    private AllFileTypes $fileTypes;
    private array $lang;

    public function __construct(
        array $allowedExtensions,
        int $maxFileNameLength,
        int $maxFileSize
    ) {
        EnvLoader::load();
        $langFile = __DIR__ . '/lang/lang-' . getenv('LANG') . '.php';

        $this->allowedExtensions = $allowedExtensions;
        $this->maxFileNameLength = $maxFileNameLength;
        $this->maxFileSize = $maxFileSize;
        $this->antivirusEnabled = (bool)getenv('ENABLE_ANTIVIRUS');
        $this->enableLogging = (bool)getenv('ENABLE_LOGGING');
        $this->fileTypes = new AllFileTypes();

        if (file_exists($langFile)) {
            $this->lang = include $langFile;
        } else {
            $this->lang = [];
        }
    }

    /**
     * Validate the uploaded file
     *
     * @param string $filePath tmp file path
     * @param string $fileName uploaded file name
     * @return array validation result
     */
    public function validate(string $filePath, string $fileName): array
    {
        $startTime = microtime(true);
        try {
            if (!file_exists($filePath)) {
                throw new Exception("FileNotFound");
            }

            if (strlen($fileName) > $this->maxFileNameLength) {
                $this->lang['InvalidFileNameSize'] = sprintf($this->lang['InvalidFileNameSize'], $this->maxFileNameLength);
                throw new Exception('InvalidFileNameSize');
            }

            if (preg_match('/[\x00-\x1f\/:*?"<>|]/', $fileName)) {
                throw new Exception("InvalidCharInFileName");
            }

            $safeFileName = basename($fileName);
            $fileExtension = strtolower(pathinfo($safeFileName, PATHINFO_EXTENSION));
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $fileMimeType = $finfo->file($filePath);

            if ((filesize($filePath) / 1000) > $this->maxFileSize) {
                $this->lang['FileSizeExceeded'] = sprintf($this->lang['FileSizeExceeded'], $this->maxFileSize);
                throw new Exception('FileSizeExceeded');
            }

            if (!in_array($fileExtension, $this->allowedExtensions, true)) {
                $this->lang['InvalidFileExtension'] = str_replace('#', implode(', ', $this->allowedExtensions), $this->lang['InvalidFileExtension']);
                throw new Exception('InvalidFileExtension');
            }

            if (!($fileMimeType === 'application/octet-stream' && $fileExtension === 'xlsx')) {
                if ($fileMimeType !== $this->fileTypes->checkExtensionWithMime($fileExtension)) {
                    throw new Exception('InvalidFileType');
                }
            }

            $pattern = '/\b(?:SELECT|INSERT|UPDATE|DELETE|CREATE|ALTER|DROP|TRUNCATE|GRANT|REVOKE)\b|<script[^>]*>(.*?)<\/script>|<\?php(.*?)\?>/is';
            if (preg_match($pattern, file_get_contents($filePath))) {
                throw new Exception('InvalidContents');
            }

            if ($this->antivirusEnabled) {
                $this->runAntivirusCheck($filePath);
            }

            $endTime = microtime(true);
            return ['time' => ($endTime - $startTime) * 1000, 'result' => "success"];
        } catch (Exception $e) {
            return ['error' => $this->lang[$e->getMessage()] ?? $e->getMessage()];
        }
    }

    /**
     * Run antivirus check using ClamAV
     *
     * @param string $filePath
     * @throws Exception
     */
    private function runAntivirusCheck(string $filePath): void
    {
        $clamavPath = getenv('ANTIVIRUS_PATH');                                    
        if (!file_exists($clamavPath)) {
            throw new Exception("AntivirusFileNotFound");
        }

        $escapedFilePath = escapeshellarg($filePath);
        $pythonPath = getenv('PYTHON_EXE_PATH');
        $pythonScript = getenv('PYTHON_SCRIPT');

        $command = $pythonPath . " " . $pythonScript . " " . $escapedFilePath . ' ' . escapeshellarg($clamavPath);
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
}

