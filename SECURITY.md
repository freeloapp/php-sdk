# Security Policy

## Supported Versions

We release patches for security vulnerabilities. Currently supported versions:

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

We take the security of the Freelo PHP SDK seriously. If you believe you have found a security vulnerability, please report it to us as described below.

### Please do NOT:

- Open a public GitHub issue for security vulnerabilities
- Disclose the vulnerability publicly before it has been addressed

### Please DO:

**Report security vulnerabilities by emailing the maintainers directly.**

You should receive a response within 48 hours. If for some reason you do not, please follow up via email to ensure we received your original message.

Please include the following information in your report:

- Type of vulnerability (e.g., authentication bypass, SQL injection, XSS, etc.)
- Full paths of source file(s) related to the vulnerability
- The location of the affected source code (tag/branch/commit or direct URL)
- Step-by-step instructions to reproduce the issue
- Proof-of-concept or exploit code (if possible)
- Impact of the vulnerability, including how an attacker might exploit it

This information will help us triage your report more quickly.

### What to Expect

- **Acknowledgment**: We will acknowledge receipt of your vulnerability report within 48 hours.
- **Communication**: We will keep you informed about the progress of fixing the vulnerability.
- **Timeline**: We aim to address critical vulnerabilities within 7 days and non-critical ones within 30 days.
- **Credit**: We will credit you in the release notes when we publish the fix (unless you prefer to remain anonymous).

## Security Best Practices

When using the Freelo PHP SDK, we recommend following these security best practices:

### 1. Protect Your API Credentials

- **Never** commit API keys or credentials to version control
- Use environment variables or secure configuration management (e.g., `.env` files, vault systems)
- Rotate API keys regularly
- Use different API keys for different environments (development, staging, production)

### 2. Validate Webhook Signatures

When handling webhooks from Freelo:

```php
use Freelo\Webhook\WebhookHandler;

$handler = new WebhookHandler($secret);

try {
    $event = $handler->handle($payload, $signature);
    // Process verified webhook event
} catch (WebhookException $e) {
    // Invalid signature - reject the webhook
}
```

Always verify webhook signatures to prevent spoofing attacks.

### 3. Use HTTPS

Always use HTTPS when making API requests. The SDK defaults to HTTPS, but ensure your environment doesn't override this.

### 4. Handle Errors Securely

Don't expose detailed error messages to end users, as they may contain sensitive information. Log errors securely and show generic error messages to users.

```php
try {
    $project = $freelo->projects()->get($id);
} catch (FreeloException $e) {
    // Log the full error securely
    error_log($e->getMessage());

    // Show generic message to users
    echo "Unable to load project. Please try again.";
}
```

### 5. Keep Dependencies Updated

Regularly update the SDK and its dependencies to get the latest security patches:

```bash
composer update freelo/php-sdk
```

### 6. Rate Limiting

Respect API rate limits to prevent service disruption. The SDK includes built-in rate limit handling, but ensure you're not making excessive requests.

### 7. Principle of Least Privilege

Use API keys with the minimum required permissions for your application's needs.

## Security Updates

Security updates will be released as patch versions and documented in the [CHANGELOG](CHANGELOG.md). We recommend subscribing to release notifications on GitHub to stay informed.

## Scope

This security policy applies to the Freelo PHP SDK codebase. For security issues with the Freelo API itself, please contact Freelo directly through their official support channels.
