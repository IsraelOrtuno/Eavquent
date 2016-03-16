Eavquent - EAV modeling for Eloquent
======================================
[![Build Status](https://travis-ci.org/IsraelOrtuno/Eavquent.svg?branch=master)](https://travis-ci.org/IsraelOrtuno/Eavquent)

This package will help you to provide an EAV structure and functionality to your Eloquent models.

- [Introduction](#introduction)
- [Install](#install)
  - [Set up with Laravel](#laravel-setup)
  - [Framework agnositc](#framework-agnostic)
- [Configuring the Eloquent model](#configuring-eloquent)
- [Registering attributes](#registering-attributes)
  - [Creating your own value types](#creating-value-types)

<a name="introduction"></a>
## Introduction

<a name="install"></a>
## Install

You can install the package via composer require command:

```shell
composer require devio/eavquent
```

Or simply add it to your `composer.json` dependences and run composer update:

```json
"require": {
    "devio/eavquent": "dev-master"
}
```

<a name="laravel-setup"></a>
### Laravel set up

If you are using Laravel just include the `EavquentServiceProvider` to the providers array in `config/app.php`. It will bootstrap the package for us:

```php
'providers' => [
    ...
    Devio\Eavquent\EavquentServiceProvider::class
    ...
]
```

Once the Service Provider is loaded, we just have to publish the package assets:

```shell
php artisan vendor:publish --provider="Devio\Eavquent\EavquentServiceProvider"
```

And then just run the database migrations:

```shell
php artisan migrate
```

<a name="framework-agnostic"></a>
### Framework agnostic set up

In case you are using Eloquent out of a Laravel/Lumen application, we need a little bit more of set up...

TODO...

<a name="configuring-eloquent"></a>
## Configuring the Eloquent model

Eavquent has been specially made for Eloquent and simplicity has been taken very serious as in any other Laravel related aspect. To add EAV functionality to your Eloquent model just define it like this:

```php
class Company extends Model 
{
    use Devio\Eavquent\EntityAttributeValues;
}
```

That's it, we only have to include that trait in our Eloquent model!

<a name="registering-attributes"></a>
## Registering attributes

<a name="creating-value-types"></a>
### Registering your own value types
