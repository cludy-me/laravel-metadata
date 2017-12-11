# Fluent Meta Data for Eloquent Models

HasMetaData Trait adds the ability to access meta data as if it is a property on your model.
HasMetaData is Fluent, just like using an eloquent model attribute you can set or unset metas. Follow along the documentation to find out more.

## Requirements

- PHP >= 7.0
- Laravel >= 5.5

## Installation

Add laravel-metadata to your composer.json file:

```json
"require": {
    "cludy-me/laravel-metadata": "dev-master"
}
```

or get composer to install the package:

```
$ composer require cludy-me/laravel-metadata
```

After updating composer, add the service provider to the `providers` array in `config/app.php`

```php
CludyMe\MetaData\MetaDataServiceProvider::class,
```
