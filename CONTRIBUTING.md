# Contributing to Freelo PHP SDK

Thank you for your interest in contributing to the Freelo PHP SDK! This document provides guidelines and instructions for contributing.

## Code of Conduct

By participating in this project, you agree to maintain a respectful and inclusive environment for everyone.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates. When creating a bug report, include:

- A clear, descriptive title
- Detailed steps to reproduce the issue
- Expected behavior vs actual behavior
- PHP version and operating system
- Code samples if applicable
- Stack traces or error messages

### Suggesting Enhancements

Enhancement suggestions are welcome! Please provide:

- A clear description of the enhancement
- Use cases that would benefit from this feature
- Possible implementation approach
- Any potential drawbacks or alternatives

### Pull Requests

1. Fork the repository
2. Create a new branch (`git checkout -b feature/my-feature`)
3. Make your changes
4. Write or update tests
5. Ensure all tests pass
6. Update documentation if needed
7. Commit your changes with clear messages
8. Push to your fork
9. Create a Pull Request

## Development Setup

1. Clone the repository:
```bash
git clone https://gitlab.com/freeloapp/php-sdk.git
cd php-sdk
```

2. Install dependencies:
```bash
composer install
```

3. Create a `.env` file for integration tests (optional):
```bash
cp .env.example .env
# Add your Freelo API credentials
```

## Development Workflow

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test:coverage

# Run specific test suite
vendor/bin/phpunit tests/Unit
vendor/bin/phpunit tests/Integration
```

### Code Quality

```bash
# Run PHPStan (static analysis)
composer phpstan

# Run PHPCS (code style check)
composer phpcs

# Fix code style automatically
composer phpcs:fix

# Run all quality checks
composer analyse
```

### Pre-commit Checklist

Before committing, ensure:

- [ ] All tests pass (`composer test`)
- [ ] PHPStan analysis passes (`composer phpstan`)
- [ ] Code follows PSR-12 standard (`composer phpcs`)
- [ ] New features have tests
- [ ] Documentation is updated if needed

## Coding Standards

### PSR Standards

- Follow PSR-12 coding style
- Use PSR-4 autoloading
- Implement PSR-18 for HTTP clients
- Use PSR-7 for HTTP messages
- Use PSR-16 for caching

### PHP Version

- Minimum PHP 8.1
- Use modern PHP features (readonly properties, enums, etc.)
- Use strict types (`declare(strict_types=1)`)

### Type Hints

- Always use type hints for parameters and return types
- Use union types where appropriate
- Document complex types with PHPDoc

### Naming Conventions

- Classes: `PascalCase`
- Methods: `camelCase`
- Properties: `camelCase`
- Constants: `UPPER_SNAKE_CASE`
- Enums: `PascalCase` for enum itself, `PascalCase` for cases

### Documentation

- Add PHPDoc blocks to all public methods and classes
- Include parameter descriptions
- Include return type descriptions
- Add usage examples for complex functionality
- Update README.md for user-facing changes

### Testing

- Write tests for all new features
- Aim for 80%+ code coverage
- Use descriptive test method names
- Follow Arrange-Act-Assert pattern
- Mock external dependencies in unit tests

## Project Structure

```
src/
├── Freelo.php              # Main SDK facade
├── Config.php              # Configuration
├── Auth/                   # Authentication
├── Http/                   # HTTP layer
├── Model/                  # Domain models
├── Resource/               # Resource managers
├── Exception/              # Exceptions
└── Enum/                   # Enums

tests/
├── Unit/                   # Unit tests
└── Integration/            # Integration tests
```

## Commit Messages

Follow conventional commit format:

```
type(scope): brief description

Detailed explanation if needed
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

Examples:
```
feat(tasks): add support for task priorities
fix(auth): correct API key header format
docs(readme): update installation instructions
test(projects): add tests for project creation
```

## Versioning

This project follows [Semantic Versioning](https://semver.org/):

- MAJOR version for incompatible API changes
- MINOR version for backwards-compatible functionality
- PATCH version for backwards-compatible bug fixes

## Release Process

1. Update `CHANGELOG.md`
2. Update version in relevant files
3. Create a git tag
4. Push tag to trigger release workflow
5. GitHub Actions will handle the rest

## Questions?

If you have questions:

- Check existing documentation
- Search existing issues
- Create a new issue with the "question" label

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

Thank you for contributing to Freelo PHP SDK!
