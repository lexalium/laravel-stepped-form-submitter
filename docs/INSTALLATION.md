# Installation

Via Composer

```
composer require lexal/laravel-stepped-form-submitter
```

## Additional changes for Lumen framework

Add the following snippet to the `bootstrap/app.php` file under the providers
section as follows:

```php
$app->register(Lexal\LaravelSteppedFormSubmitter\ServiceProvider\ServiceProvider::class);
```
