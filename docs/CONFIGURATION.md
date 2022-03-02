# Configuration

## Publish the config

Run the following command to publish the package config file:

```shell
php artisan vendor:publish --provider="Lexal\LaravelSteppedFormSubmitter\ServiceProvider\ServiceProvider"
```

You can update the following options in the `config/form-submitter.php` file:

1. The flag `use_transactional` shows do we need or not transactions on the
   form submitting.

```php
'use_transactional' => false,
```

2. Your custom form submitters that the service provider will pass to the
   `Lexal\FormSubmitter\FormSubmitter` constructor.

```php
'submitters' => [
    // list of form submitters
],
```
