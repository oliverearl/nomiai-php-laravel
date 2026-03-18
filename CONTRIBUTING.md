# Contributing to Nomi.ai Laravel Wrapper

Thank you for considering contributing to the Nomi.ai Laravel wrapper! We welcome contributions from the community.

## Code of Conduct

Please be respectful and constructive in all interactions with the project and its community.

## Development Setup

### Prerequisites

- PHP 8.3 or higher with JSON extension
- Composer
- Laravel 11, 12, or 13

### Installation

1. Fork and clone the repository:
```bash
git clone https://github.com/YOUR-USERNAME/nomiai-php-laravel.git
cd nomiai-php-laravel
```

2. Install dependencies:
```bash
composer install
```

3. You're ready to develop!

## Development Workflow

### Running Tests

We use [Pest](https://pestphp.com/) for testing:

```bash
composer test              # Run full test suite
composer test-coverage     # Run with coverage (requires Xdebug or PCOV)
```

All pull requests must pass the existing test suite.

### Code Style

We use Laravel Pint with the PER (PHP Evolving Recommendations) preset:

```bash
composer format
```

**Always run this before committing!** The CI pipeline will check code style automatically.

### Static Analysis

We use PHPStan (via Larastan) at level 5:

```bash
composer analyse
```

Your code should pass static analysis without errors.

## Coding Standards

### Critical Conventions

1. **Strict Types**: Every PHP file MUST start with `declare(strict_types=1);` after the opening tag
2. **Namespace**: Use `Nomiai\PhpSdk\Laravel\*` (note: `PhpSdk` not `PHPSdk`)
3. **Strict Equality**: Use `===` and `!==`, never `==` or `!=`
4. **PER Code Style**: Follow the PER preset (enforced by Pint)

These are enforced by architecture tests in `tests/ArchTest.php`.

### PHPDoc Requirements

- Add PHPDoc blocks for all public methods
- Include `@param` tags with types
- Include `@return` tags with types
- Document exceptions with `@throws` tags
- Use `@inheritDoc` when implementing interface methods

See `AGENTS.md` for detailed project conventions and architecture patterns.

## Making Changes

### For Bug Fixes

1. Create an issue describing the bug (if one doesn't exist)
2. Create a branch from `main`: `git checkout -b fix/issue-description`
3. Make your changes
4. Add tests that verify the fix
5. Run `composer test`, `composer analyse`, and `composer format`
6. Submit a pull request

### For New Features

1. Open an issue to discuss the feature first
2. Wait for approval from maintainers
3. Create a branch from `main`: `git checkout -b feature/feature-name`
4. Implement the feature
5. Add comprehensive tests (aim for 100% coverage of new code)
6. Update documentation (README.md, AGENTS.md if architectural)
7. Run `composer test`, `composer analyse`, and `composer format`
8. Submit a pull request

## Pull Request Guidelines

- **One logical change per PR** - Keep changes focused
- **Write clear commit messages** - Explain what and why, not just how
- **Update tests** - All new functionality requires tests
- **Update documentation** - Keep README.md current
- **Pass CI checks** - All automated checks must pass
- **Link related issues** - Reference issue numbers in PR description

### Pull Request Checklist

Before submitting, verify:

- [ ] Tests pass (`composer test`)
- [ ] Static analysis passes (`composer analyse`)
- [ ] Code is formatted (`composer format`)
- [ ] New features have tests
- [ ] Documentation is updated
- [ ] CHANGELOG.md is updated (for version releases)
- [ ] Commit messages are clear

## Testing Guidelines

### Test Structure

- Use descriptive test names: `it('handles error responses correctly')`
- Test both happy paths and edge cases
- Use `Http::fake()` for mocking HTTP requests
- Keep tests isolated and independent

### Test Coverage Priorities

1. **Critical paths**: API authentication, request/response handling
2. **Error handling**: Exceptions, validation failures
3. **Edge cases**: Empty values, null values, malformed data
4. **Integration points**: Service provider, facade, adapter

## Architectural Considerations

This is a **Laravel wrapper package**, not a standalone application:

- The package wraps the base SDK (`oliverearl/nomiai-php`)
- It provides Laravel-specific integration (facades, config, HTTP adapter)
- Actual API functionality lives in the base SDK
- The adapter bridges Laravel's `Http` facade with Guzzle's `ClientInterface`

When making changes, consider:

- Will this affect Http::fake() mocking?
- Does this maintain compatibility with the base SDK?
- Is this Laravel-specific or should it live in the base SDK?

## Reporting Bugs

Use the GitHub issue tracker:

1. Search existing issues first
2. Use the bug report template
3. Include:
   - PHP version
   - Laravel version
   - Steps to reproduce
   - Expected vs actual behavior
   - Stack traces if applicable

## Security Vulnerabilities

**Do not open public issues for security vulnerabilities.**

See [SECURITY.md](SECURITY.md) for responsible disclosure procedures.

## Questions?

- Check [README.md](README.md) for usage documentation
- Check [AGENTS.md](AGENTS.md) for architecture details
- Open a discussion on GitHub for general questions
- Review existing issues and pull requests

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

## Acknowledgments

Thank you for helping improve the Nomi.ai Laravel wrapper! Every contribution, whether it's code, documentation, or bug reports, helps make this package better for everyone.

