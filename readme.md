# PwnedPasswords

**PwnedPasswords** is a library that allows you to query [Troy Hunt's Pwned Passwords API](https://haveibeenpwned.com/Passwords) to determine if a password has been compromised in a public data breach.

---

## Requirements

- PHP **>= 8.2**

---

## Installation

Install PwnedPasswords easily with Composer by running the following command in your project directory:

```bash
composer require oldas/pwned-passwords
```

---

## Usage

First, include the Composer `autoload.php` to load the library:

```php
require_once('vendor/autoload.php');
```

Then, use the core service class `HaveIBeenPwnedService` to interact with the Pwned Passwords API:

```php
use Oldas\PwnedPasswords\HaveIBeenPwnedService;

// Create a service instance
$haveIBeenPwnedService = new HaveIBeenPwnedService();

$plainTextPassword = 'password'; // leaked password

// Check if the password has been compromised
$result = $haveIBeenPwnedService->isPwned($plainTextPassword); 
// Returns: true (if compromised), false (if safe), or null (in case of API timeout)

// Validate the password (throws exceptions for invalid input)
$haveIBeenPwnedService->validatePassword($plainTextPassword);  // Throws InvalidPasswordInputException, otherwise returns void
```

### Methods Explained

#### `isPwned(string $plainTextPassword): ?bool`
This method checks whether the given password has been exposed in a public data breach by querying the **Pwned Passwords API**.

- **Returns:**
    - `true`: The password was found in a breach.
    - `false`: The password was not found in a breach.
    - `null`: The API call timed out or failed.

#### `validatePassword(string $plainTextPassword): void`
This method ensures the password meets the library's input criteria. If the password is invalid, it throws an exception before performing any further operations.

- **Throws:** `InvalidPasswordInputException`

---

## Notes

- The library uses the **[k-anonymity](https://en.wikipedia.org/wiki/K-anonymity)** technique to query the API securely without revealing the full password to external services.
- Ensure proper validation and exception handling in your implementation to cover cases such as API timeout or invalid input.

---

## License

This project is released under the [MIT License](https://opensource.org/licenses/MIT).

---

## Resources

- [Troy Hunt's PwnedPasswords API Documentation](https://haveibeenpwned.com/API/v3)
- [PHP Composer Documentation](https://getcomposer.org)