# SecureUpload

SecureUpload is a secure file upload library for PHP that ensures files are safely uploaded to your server by performing a series of security validations. It includes checks for file existence, extension and MIME type validation, content scanning for malicious code, and optional antivirus scanning via ClamAV.

## Features

- **File Existence Check:** Ensures the uploaded file exists before processing.
- **Extension & MIME Type Validation:** Verifies that files have valid extensions and corresponding MIME types.
- **Content Scanning:** Detects and prevents malicious scripts or code embedded in files.
- **Antivirus Integration:** Uses ClamAV (triggered via a Python script) to scan files for threats, with logging support if enabled.
- **PSR-4 Autoloading:** Fully compliant with Composer autoloading standards for easy integration.

## Requirements

- **PHP:** Version 7.4 or higher. ([PHP Official Website](https://www.php.net))
- **Python:** Required for antivirus scanning. ([Python Official Website](https://www.python.org))
- **ClamAV:** For antivirus scanning:
  - **macOS:** Install via [Homebrew](https://brew.sh) using:  
    ```bash
    brew install clamav
    ```
  - **Linux:** Install using your distribution's package manager. For Ubuntu, for example:  
    ```bash
    sudo apt-get install clamav
    ```
  - **Windows:** Download from the [ClamAV website](https://www.clamav.net/downloads#otherversions) and follow the installation instructions.

## Installation

SecureUpload is available via Composer. To install, run the following command in your project directory:

```bash
composer require farzad-forouzanfar/secure-upload
```

Alternatively, clone the repository:
1.  Clone the repository:
    
   ```bash
   git clone https://github.com/FarzadForuozanfar/SecureUpload.git
   ```
    
2.  Navigate to the project directory:
    
   ```bash
   cd SecureUpload
   ```
    
3.  Install dependencies via Composer:
    
   ```bash
   composer install
   ```

## Configuration

1.  **Environment Variables:**  
    Create or update your `.env` file with the necessary configuration settings.
    
2.  **Language Files:**  
    Place your language files in the `lang/` directory (e.g., `lang/lang-en.php` or `lang/lang-fa.php`).
    
3.  **Web Server Setup:**  
    Configure your web server to serve the `public/` directory as the document root.

## Usage
To use SecureUpload, simply include the Composer autoloader in your project and instantiate the uploader in your application code. For example, in your `public/index.php`

```php
<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use SecureUpload\FileTypes\ImageTypes; 
use SecureUpload\Interfaces\FileSize; 
use SecureUpload\Uploader\SecureUploader; 

if (!empty($_FILES['uploaded_file'])) 
{
    // Define the allowed extensions and file size limits
    $allowedExtensions = ImageTypes::getAllExtensions(); // Get all allowed extensions for images
    $maxFileNameLength = 50; // Maximum file name length
    $maxFileSize = FileSize::TEN_MG; // Max file size (10MB)
    
    // Instantiate the SecureUploader with the configuration
    $uploader = new SecureUploader($allowedExtensions, $maxFileNameLength, $maxFileSize); 
    // Reorganize the files array for processing
    $files = []; 
    foreach ($_FILES['uploaded_file'] as $key => $items) 
    { 
        foreach ($items as $index => $item) 
        {
            $files[$index][$key] = $item; 
        } 
    }
     
    // Validate each uploaded file
    foreach ($files as $file) 
    { 
        $result = $uploader->validate($file['tmp_name'], $file['name']); 
        if (isset($result['error'])) 
        { // Print the error message if validation fails
            echo "Error: " . $result['error']; die(); 
        } 
        else 
        { // Print the success message if validation passes
            echo "File uploaded successfully: " . $file['name']; 
        } 
    } 

else 
{
    echo "No file uploaded.";
} 
?>
```

## Usage via CLI
This package also provides a CLI tool that you can use for quick testing and configuration.

### 🔧 Publish `.env` file
To publish the default `.env` configuration file into your project root:

   ```bash
   php vendor/bin/secure-upload publish-env
   ```
### 🧪 Test File Upload Validation
You can quickly test the validation logic via CLI using a file path:

   ```bash
   php vendor/bin/secure-upload test-upload --file=path/to/your/file.jpg
   ```

## Contributing
Contributions are welcome! If you encounter a bug or have a feature request, please open an issue on the [GitHub repository](https://github.com/FarzadForuozanfar/SecureUpload/issues). To contribute code, fork the repository and submit a pull request.

## License
SecureUpload is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.
![image_2025-04-06_14-34-264](https://github.com/user-attachments/assets/2562e0a2-4e72-4e80-a077-e6936bf285b4)

![image_2025-04-06_14-34-26](https://github.com/user-attachments/assets/e57a43b3-cbac-48fe-9007-513887a8df8c)
