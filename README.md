Laravel 5.3.x Pwned Validator
=============================

Pwned Validator to extend Validation support with HIBP password lists. Secure your users by determining if the password is secure or comprimised.

Installation
------------

```
composer require nexusvc/laravel-pwned
```

Pwned Validation
----------------
Extends form validation with 'pwned'

```
'password' => 'required|string|min:6|pwned';
```
