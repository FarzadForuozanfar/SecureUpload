# SecureUpload

SecureUpload is a web application that allows users to securely upload files to a server. The application includes several security features to ensure that uploaded files do not contain malicious code that could harm the server or other users.

## Features

- File existence check: uploaded files are checked to ensure that they exist in memory before being processed.
- Extension and mime type check: uploaded files are checked to ensure that they have a valid file extension and mime type.
- Content check: uploaded files are checked for malicious scripts or code.
- Antivirus check: uploaded files are checked using the ClamAV antivirus, which is called from a Python script using `exec()` in PHP. The antivirus results are printed in the console and logged in PHP if logging is enabled.

## Installation

To install SecureUpload, follow these steps:

1. Clone the SecureUpload repository to your server.
2. Make sure that <a href="https://www.php.net" > PHP <a/> and <a href="https://www.python.org" > PYTHON <a/> is installed on your server.
3. Install <a href="https://www.clamav.net" > ClamAV <a/> ClamAV on your server:
   - macOS: ClamAV can be installed using [Homebrew](https://brew.sh/). Run the following command in your terminal: `brew install clamav`
   - Linux: ClamAV can be installed using your distribution's package manager. For example, on Ubuntu, run the following command in your terminal: `sudo apt-get install clamav`
   - Windows: ClamAV can be downloaded from the [ClamAV website](https://www.clamav.net/downloads#otherversions). Follow the instructions provided to install ClamAV on your system.
4. Configure your web server to serve the SecureUpload files.
5. Create a directory on your server where uploaded files will be stored.
6. Update the `upload.php` file with the path to the directory where uploaded files will be stored.

## Usage

To use SecureUpload, simply navigate to the upload page in your web browser and select a file to upload. The uploaded file will be checked for security risks before being added to the server.

## Contributing

Contributions to SecureUpload are welcome! If you find a bug or have a feature request, please open an issue on the repository. If you would like to contribute code, please fork the repository and submit a pull request.

## License

SecureUpload is licensed under the MIT License. See the LICENSE file for more information.
