# Configuration

## Publish the config

Run the following command to publish the package config file:

```shell
php artisan vendor:publish --provider="Lexal\LaravelSteppedFormSubmitter\ServiceProvider\ServiceProvider"
```

You can update the following options in the `config/form-submitter.php` file:

1. Use `transaction_class` option to place a class name, instance or service
   alias which the FormSubmitter will use to handle transactions. Place null
   or remove config to disable transactions.

```php
'transaction_class' => false,
```

2. Your custom form submitters that the service provider will pass to the
   `Lexal\FormSubmitter\FormSubmitter` constructor.

```php
'submitters' => [
    // list of form submitters
],
```
