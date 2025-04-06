## Contributing to SecureUpload

Thank you for considering contributing to **SecureUpload**! 🚀

We welcome contributions that make SecureUpload better, more reliable, and easier to use. Whether you're fixing bugs, improving documentation, or adding new features — you're awesome!

---

### 🧱 Project Structure

```
secure-upload/
├── bin/                  # CLI scripts
├── src/                  # Main source code
│   └── Cli\              
│   └── Config\              
│   └── Env\              
│   └── FileTypes\              
│   └── Interfaces\              
│   └── Scripts\              
│   └── Uploader\           
├── lang/                 # Language files
├── tests/                # Unit tests
├── .env.example          # Example env configuration
├── composer.json         # Composer package config
├── README.md             # Main documentation
└── CONTRIBUTING.md       # Contribution guidelines
```

---

### 🛠 Development Setup

1. Clone your fork locally:

```bash
git clone https://github.com/YOUR_USERNAME/SecureUpload.git
cd SecureUpload
```

2. Install dependencies:

```bash
composer install
```

3. (Optional) Run tests:

```bash
vendor/bin/phpunit
```

4. Run CLI command during development:

```bash
php bin/secure-upload test-upload --file=path/to/file.jpg
```

---

### 📦 Using CLI after installation via Composer

If you're installing SecureUpload in another project via:

```bash
composer require farzad-forouzanfar/secure-upload
```

Then the CLI command becomes available:

```bash
php vendor/bin/secure-upload test-upload --file=storage/test.pdf
```

Make sure the autoload path is resolved properly in the CLI script using this pattern:

```php
require_once __DIR__ . '/../../../autoload.php';
```

---

### 🔍 Contributing Code

1. Create a branch:

```bash
git checkout -b fix/something-cool
```

2. Make your changes and commit:

```bash
git commit -m "Fix: add support for X"
```

3. Push and open a PR:

```bash
git push origin fix/something-cool
```

4. Open a pull request to the `main` branch on GitHub.

---

### 🧪 Running Tests

We use [PHPUnit](https://phpunit.de/) for testing. You can run all tests with:

```bash
vendor/bin/phpunit
```

---

### 💬 Need Help?

Feel free to open an issue if you run into any problems or have questions. We appreciate your feedback!

---

### ❤️ Thank You!

Thanks again for contributing to SecureUpload. You make open source better!

