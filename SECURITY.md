# Security Policy

## Supported Versions

We actively support the following versions with security updates:

| Version | Supported          | PHP       | Laravel        |
| ------- | ------------------ | --------- | -------------- |
| 1.1.x   | :white_check_mark: | ^8.3      | ^11.0\|^12.0\|^13.0 |
| 1.0.x   | :white_check_mark: | ^8.3      | ^11.0          |
| < 1.0   | :x:                | -         | -              |

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

Instead, please report security vulnerabilities by email to:

**oliver@oliverearl.co.uk**

### What to Include

Please include the following information in your report:

1. **Description**: A clear description of the vulnerability
2. **Impact**: What an attacker could achieve
3. **Steps to Reproduce**: Detailed steps to reproduce the issue
4. **Affected Versions**: Which versions are affected
5. **Proposed Solution**: If you have one, include a suggested fix
6. **Your Details**: Name and contact info for acknowledgment (optional)

### What to Expect

1. **Acknowledgment**: You'll receive a response within **48 hours** acknowledging receipt
2. **Assessment**: We'll assess the vulnerability and determine severity
3. **Updates**: We'll keep you informed of progress (at least weekly)
4. **Resolution**: Once confirmed, we'll:
   - Develop and test a fix
   - Prepare a security advisory
   - Release a patched version
   - Credit you in the advisory (unless you prefer to remain anonymous)

### Timeline

- **Critical vulnerabilities**: Patched within 7 days
- **High severity**: Patched within 14 days
- **Medium/Low severity**: Patched in next scheduled release

## Security Best Practices

### For Package Users

1. **Keep Updated**: Always use the latest version
2. **Secure API Keys**: Never commit `NOMIAI_API_KEY` to version control
3. **Environment Variables**: Store sensitive data in `.env` files
4. **HTTPS Only**: Always use HTTPS endpoints (default: `https://api.nomi.ai`)
5. **Dependency Audits**: Regularly run `composer audit` to check for known vulnerabilities

### For Contributors

1. **No Secrets in Code**: Never commit API keys, tokens, or credentials
2. **Sanitize Logs**: Ensure sensitive data isn't logged
3. **Validate Input**: Always validate and sanitize user input
4. **Secure Dependencies**: Keep all dependencies up to date
5. **Review Code**: Check for common vulnerabilities (SQL injection, XSS, etc.)

## Known Security Considerations

### API Token Handling

- API tokens are passed in the `Authorization` header
- Tokens are stored in config (pulled from environment variables)
- The package throws `NomiaiException` if token is missing/empty
- Tokens are never logged or exposed in error messages

### HTTP Client Security

- Uses Laravel's HTTP client with `Http::fake()` support
- `RequestOptions::HTTP_ERRORS => false` prevents exceptions on HTTP errors
- All requests use HTTPS by default
- Custom User-Agent header identifies the SDK version

### Laravel Integration

- Service provider binds singleton instance
- Config uses `env()` only in config file (per Laravel best practices)
- No database interactions (stateless)
- Compatible with Laravel Octane

## Security Disclosure Policy

When a security vulnerability is disclosed:

1. **Private Fix**: We develop a fix in a private repository
2. **Security Advisory**: We prepare a GitHub security advisory
3. **Coordinated Release**: We release the fix and advisory simultaneously
4. **Notification**: We notify users via:
   - GitHub security advisory
   - Release notes
   - CHANGELOG.md
5. **Credit**: We credit the reporter (unless they prefer anonymity)

## Out of Scope

The following are outside our security scope:

- Vulnerabilities in the base SDK (`oliverearl/nomiai-php`) - report to that repository
- Vulnerabilities in Laravel framework - report to Laravel
- Vulnerabilities in the Nomi.ai API itself - report to Nomi.ai
- Social engineering attacks
- Physical attacks
- Denial of service (DoS) attacks

## Contact

- **Security Issues**: oliver@oliverearl.co.uk
- **General Questions**: Open a GitHub Discussion
- **Bug Reports**: Open a GitHub Issue

## PGP Key

For particularly sensitive disclosures, PGP encrypted email is available upon request.

---

**Last Updated**: March 18, 2026

Thank you for helping keep the Nomi.ai Laravel wrapper secure!

