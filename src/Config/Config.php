<?php

namespace SecureUpload\Config;

class Config
{
    public array $allowedExtensions;
    public int $maxFileNameLength;
    public int $maxFileSize;
    public bool $enableAntivirus;
    public bool $enableLogging;
    public string $antivirusPath;
    public string $pythonPath;
    public string $pythonScript;
    public string $language;

    public function __construct()
    {
        $this->allowedExtensions = explode(',', getenv('ALLOWED_EXTENSIONS') ?: '');
        $this->maxFileNameLength = (int) getenv('MAX_FILE_NAME_LENGTH') ?: 50;
        $this->maxFileSize = (int) getenv('MAX_FILE_SIZE') ?: 10240; 
        $this->enableAntivirus = filter_var(getenv('ENABLE_ANTIVIRUS'), FILTER_VALIDATE_BOOLEAN);
        $this->enableLogging = filter_var(getenv('ENABLE_LOGGING'), FILTER_VALIDATE_BOOLEAN);
        $this->antivirusPath = getenv('ANTIVIRUS_PATH') ?: '/usr/bin/clamscan';
        $this->pythonPath = getenv('PYTHON_EXE_PATH') ?: 'python3';
        $this->pythonScript = getenv('PYTHON_SCRIPT') ?: 'scan.py';
        $this->language = getenv('LANG') ?: 'en';
        $this->language = explode(' ', $this->language)[0];
        $this->language = in_array($this->language, ['en', 'fa']) ? $this->language : 'en';
    }
}
