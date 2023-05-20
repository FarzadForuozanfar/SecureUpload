<?php
class ImageTypes
{
    const Image = [
        ['mime' => 'image/jpeg', 'ext' => 'jpeg'],
        ['mime' => 'image/jpg', 'ext' => 'jpg'],
        ['mime' => 'image/png', 'ext' => 'png'],
        ['mime' => 'image/gif', 'ext' => 'gif'],
        ['mime' => 'image/tiff', 'ext' => 'tiff'],
        ['mime' => 'image/svg+xml', 'ext' => 'svg'],
        ['mime' => 'image/x-ms-bmp', 'ext' => 'bmp'],
        ['mime' => 'application/octet-stream', 'ext' => 'psd']

    ];

    public static function getAllExtensions()
    {
        $extensions = array();
        foreach (self::Image as $item) {
            $ext = $item['ext'];
            if (!in_array($ext, $extensions)) {
                $extensions[] = $ext;
            }
        }
        return $extensions;
    }

    public static function getAllMimes()
    {
        $mimes = array();
        foreach (self::Image as $item) {
            $mimes[] = $item['mime'];
        }
        return $mimes;
    }

    /**
     * @param array $extensions
     * @return array 
     */
    public static function getAllTypesExcept($extensions)
    {
        $types = array();
        foreach (self::Image as $item) {
            if (!in_array($item['ext'], $extensions)) {
                $types[] = $item;
            }
        }
        return $types;
    }
}

class TxtDocTypes
{
    const Docs = [
        ['mime' => 'text/csv', 'ext' => 'csv'],
        ['mime' => 'text/plain', 'ext' => 'txt'],
        ['mime' => 'application/msword', 'ext' => 'doc'],
        ['mime' => 'application/vnd.ms-excel', 'ext' => 'xls'],
        ['mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'ext' => 'xlsx'],
        ['mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'ext' => 'docx'],
        ['mime' => 'application/pdf', 'ext' => 'pdf'],
        ['mime' => 'text/rtf', 'ext' => 'rtf'],
    ];

    public static function getAllExtensions()
    {
        $extensions = array();
        foreach (self::Docs as $item) {
            $ext = $item['ext'];
            if (!in_array($ext, $extensions)) {
                $extensions[] = $ext;
            }
        }
        return $extensions;
    }

    public static function getAllMimes()
    {
        $mimes = array();
        foreach (self::Docs as $item) {
            $mimes[] = $item['mime'];
        }
        return $mimes;
    }

    /**
     * @param array $extensions
     * @return array 
     */
    public static function getAllTypesExcept($extensions)
    {
        $types = array();
        foreach (self::Docs as $item) {
            if (!in_array($item['ext'], $extensions)) {
                $types[] = $item;
            }
        }
        return $types;
    }
}

class CompressedTypes
{
    const Comp = [
        ['mime' => 'application/zip', 'ext' => 'zip'],
        ['mime' => 'application/x-rar', 'ext' => 'rar'],
    ];

    public static function getAllExtensions()
    {
        $extensions = array();
        foreach (self::Comp as $item) {
            $ext = $item['ext'];
            if (!in_array($ext, $extensions)) {
                $extensions[] = $ext;
            }
        }
        return $extensions;
    }

    public static function getAllMimes()
    {
        $mimes = array();
        foreach (self::Comp as $item) {
            $mimes[] = $item['mime'];
        }
        return $mimes;
    }

    /**
     * @param array $extensions
     * @return array 
     */
    public static function getAllTypesExcept($extensions)
    {
        $types = array();
        foreach (self::Comp as $item) {
            if (!in_array($item['ext'], $extensions)) {
                $types[] = $item;
            }
        }
        return $types;
    }
}

class AllFileTypes
{
    private static $All = [];

    public function __construct()
    {
        self::$All = array_merge(ImageTypes::Image, TxtDocTypes::Docs, CompressedTypes::Comp);
    }

    public static function getAllExtensions()
    {
        $extensions = array();
        foreach (self::$All as $item) {
            $ext = $item['ext'];
            if (!in_array($ext, $extensions)) {
                $extensions[] = $ext;
            }
        }
        return $extensions;
    }

    public static function getAllMimes()
    {
        $mimes = array();
        foreach (self::$All as $item) {
            $mimes[] = $item['mime'];
        }
        return $mimes;
    }

    public static function checkExtensionWithMime($extension)
    {
        foreach (self::$All as $item)
        {
            if ($item['ext'] == $extension)
                return $item['mime'];
        }

        return null;
    }

    public static function getSpecificFileType($extension)
    {
        foreach (self::$All as $item)
        {
            if ($item['ext'] == $extension)
                return $item;
        }

        return null;
    }

    /**
     * @param array $extensions
     * @return array 
     */
    public static function getAllTypesExcept($extensions)
    {
        $types = array();
        foreach (self::$All as $item) {
            if (!in_array($item['ext'], $extensions)) {
                $types[] = $item;
            }
        }
        return $types;
    }
}

interface FileSize{
    const THREE_MG  = 3072;
    const FIVE_MG   = 5120;
    const SEVEN_MG  = 7168;
    const TEN_MG    = 10240;
}

/**
 * @param string $path
 * @param string $name
 * @param array $allowed_extensions
 * @param int $max_filenameSize
 * @param int $max_size
 * @param bool $Antivirus
 * @param bool $enable_logging //log events in python script
 * @return array
 */
function CheckUploadedFile($path, $name, $allowed_extensions, $max_filenameSize, $max_size, $Antivirus = false, $enable_logging = false)
{
    try 
    {
        $start_time = microtime(true);
        if (!file_exists($path)) 
        {
            throw new Exception("File not found");
        }

        if (strlen($name) > $max_filenameSize)
        {
            throw new Exception(sprintf('InvalidFileNameSize', $max_filenameSize));
        }

        if (preg_match('/[\x00-\x1f\/:*?"<>|]/', $name))
        {
            throw new Exception("InvalidCharInFileName");
        }

        $name           = basename($name);
        $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $file_mime_type = (new finfo(FILEINFO_MIME_TYPE))->file($path);

        if ((filesize($path) / 1000) > $max_size) 
        {
            throw new Exception('FileSizeExceeded');
        }
        if (!in_array($file_extension, $allowed_extensions, true)) 
        {
            throw new Exception('InvalidFileExtension');
        }
        
        if (!($file_mime_type == 'application/octet-stream' && $file_extension == 'xlsx'))
        {
            $file = new AllFileTypes();
            
            if ($file_mime_type !== $file->checkExtensionWithMime($file_extension)) 
            {
                throw new Exception('InvalidFileType');
            }
        }
        $pattern = '/\b(?:SELECT|INSERT|UPDATE|DELETE|CREATE|ALTER|DROP|TRUNCATE|GRANT|REVOKE)\b|<script[^>]*>(.*?)<\/script>|<\?php(.*?)\?>/is';

        if (preg_match($pattern, file_get_contents($path)))
        {
            throw new Exception('InvalidContents');
        }
        if ($Antivirus)
        {
            $clamav_path = "E:\\Program Files\\ClamAV\\clamscan.exe";
            if (!file_exists($clamav_path)) 
            {
                throw new Exception("ClamAV executable file not found");
            }

            $file_path_escaped = escapeshellarg($path);

            $python_path = "C:\\Users\\win10\\AppData\\Local\\Programs\\Python\\Python310\\python.exe ";
            $python_script = "E:\\wamp64\\www\\test\\scan_file.py ";

            $command = $python_path. $python_script . $file_path_escaped . ' ' . escapeshellarg($clamav_path);
            
            exec($command, $output, $return_val);
            $out = [];
            foreach ($output as $item)
            {
                $out = json_decode($item, true);
            }

            if ($return_val === 0) 
            {
                if (trim($out['result']) !== "No threats found") 
                {
                    if (file_exists($path)) 
                    {
                        unlink($path);
                    }

                    if ($enable_logging && $out['result'] !== "No threats found")
                    {
                        // logging
                    }
                    throw new Exception('InfectedFile');
                } 
            } 
            
            else 
            {
                throw new Exception("ClamAV error: " . implode("\n", $output));
            }
        }
    } 

    catch (Exception $e) 
    {
        $error['exception'] = $e->getMessage();
    }

    $end_time = microtime(true);
    return empty($error) ? ['time' => ($end_time - $start_time) * 1000, 'result' => "success"] : $error;
}

// end func
if (!empty($_FILES['uploaded_file'])) 
{
    echo "<hr> Begin <hr>";
    $allowed_extensions = array_merge(ImageTypes::getAllExtensions(), TxtDocTypes::getAllExtensions(), CompressedTypes::getAllExtensions());
    $allowed_mime_types = array_merge(ImageTypes::getAllMimes(), TxtDocTypes::getAllMimes(), CompressedTypes::getAllMimes());
    var_dump($_FILES['uploaded_file']);

    $files = [];
    foreach ($_FILES['uploaded_file'] as $key => $items)
    {
        foreach($items as $index => $item)
        {
            $files[$index][$key] = $item;
        }
    }
    foreach ($files as $file)
    {
        
        $result = CheckUploadedFile($file['tmp_name'], $file['name'], $allowed_extensions, $allowed_mime_types, FileSize::TEN_MG, true, true);
        if (!isset($result['time']))
        {
            var_dump($result);
            die();
        }
    }
}

else
{
    echo " not uploaded". "<hr>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form enctype="multipart/form-data" method="post">
        <input multiple type="file" name="uploaded_file[]" id="file">
        <input type="submit" value="post">
    </form>
</body>

</html>