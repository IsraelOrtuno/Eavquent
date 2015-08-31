## Propertier - EAV modeling for Eloquent

[![Build Status](https://travis-ci.org/IsraelOrtuno/Propertier.svg?branch=master)](https://travis-ci.org/IsraelOrtuno/Propertier)

### Installation

#### 1. Require the package with composer

Using composer you can install the package using the `require` command:

```
composer require devio/properties
```

Or simply add it to your `composer.json` dependences and perform an update:

```
"require": {
  ...
  "devio/propertier": "~1.0"
  ...
}
```

#### 2. Add the Service Provider

Once installed, you should include the `PropertierServiceProvider` to the providers array in `config/app.php`.

```
'providers' => [
  ...
  Devio\Propertier\PropertierServiceProvider::class,
  ...
]
```

#### 3. Publish the package assets

The propertier package requires a few tables available in the database schema to work. First let's publish the database migrations:

```
php artisan vendor:publish --provider="Devio\Propertier\PropertierServiceProvider" --tag="migrations"
```

When published, just run these migrations as you would normally do:

```
php artisan migrate
```

**Optional:** The package also works with a configuration file. If you wish to modify the default configuration values, just have to publish your own:

```
php artisan vendor:publish --provider="Devio\Propertier\PropertierServiceProvider" --tag="config"
```
