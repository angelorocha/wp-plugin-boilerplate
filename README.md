# WordPress Plugin Boilerplate

This repository serves as a modern boilerplate designed for fast and standardized WordPress plugin development. It comes pre-configured with a clean architecture separating *Admin* and *Public* scopes, dependency management via Composer (with PSR-4/autoloader support), and front-end build automation using Gulp, SASS, and JavaScript/image minification. It provides a solid foundation for building scalable, high-performance plugins ready for production.

## 🚀 Development Environment & Tooling

To ensure code quality and adherence to WordPress standards, the project includes pre-configured testing and static analysis tools:
- **Testing & Quality:** Integrated setup for PHPUnit, PHPStan, and PHP_CodeSniffer (PHPCS tailored to WordPress Coding Standards).
- **Docker Support:** Containerized environment available via `.build/docker-compose.yml` for quick local WordPress setup.
- **CI/CD Pipelines:** Ready-to-use GitHub Actions workflows located in `.github/workflows/` for automated code quality checks and deployment to the official WordPress.org repository.

## 🛠️ Getting Started

1. **Setup:** Clone the repository and run `composer install` and `npm install` in the root directory to install PHP and Node.js dependencies.
2. **Asset Compilation:** Use the Gulp tasks defined in `tasks/` to compile SASS stylesheets, process JavaScript files, and optimize images from source directories (`src/`).
3. **Production Build:** Run the build script located at `.build/build.sh` (or `.build/build.ps1` on Windows) to package a clean, production-ready release omitting development assets, test files, and source code.
