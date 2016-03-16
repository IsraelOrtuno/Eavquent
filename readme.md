Eavquent - EAV modeling for Eloquent
======================================
[![Build Status](https://travis-ci.org/IsraelOrtuno/Eavquent.svg?branch=master)](https://travis-ci.org/IsraelOrtuno/Eavquent)

This package will help you to provide an EAV structure and functionality to your Eloquent models.

- [Introduction](#introduction)
  - [Performance](#performance)
- [Install](#install)
  - [Laravel set up](#laravel-setup)
  - [Framework agnositc set up](#framework-agnostic)
- [Configuring the Eloquent model](#configuring-eloquent)
  - [$morphClass and morphMap()](#morph-class)
- [Attributes](#attributes)
  - [Registering an attribute](#registering-attribute)
  - [Allowing multiple values](#attribute-collections)
- [Values](#values)
  - [Creating your own value types](#creating-value-types)
- [Getting/setting values](#getting-setting-values)
  - [Saving values](#saving-values)
  - [Accesing to relationships](#underlaying-relations)
- [Converting to array/json](#converting-array)
- [Querying models](#querying-models)
- [Eager loading](#eager-loading)
- [Events](#events)

<a name="introduction"></a>
## Introduction

<a name="performance"></a>
### Performance

<a name="install"></a>
## Install

You can install the package via composer require command:

```shell
composer require xxx/xxx
```

Or simply add it to your `composer.json` dependences and run composer update:

```json
"require": {
    "xxx/xxx": "dev-master"
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

<a name="querying-models"></a>
## Querying models

Eavquent tries to do everything in the same way Eloquent would normally do. When loading a model it internally creates a regular relationship for every entity attribute. This means we can query filtering by our registered attribute values like we would normally do when querying Eloquent relationships:

```php
// city is an eav attribute
$companies = Company::whereHas('city', function ($query) {
    $query->where('content', 'Madrid');
})->get();
```

<a name="eager-loading"></a>
## Eager loading

Eavquent takes into account the powerful Eloquent eager loading system. When accessing to a Eavquent attribute in a Eloquent model, it will be loaded just in time as Eloquent does when working with relationships. However we can work with Eavquent using Eloquent eager loading for better performance and avoid the n+1 query problem.

Eavquent has a special relationship name reserved for loading all the registered attributes. This relationship is called `eav`. When using `eav` for loading values, it will load all the attributes related to the entity we are playing with.

#### Lazy eager loading

Again, as any regular Eloquent relationship we can decide when to load our attributes. Do it as if you were normally loading a relationship:

```php
$company->load('eav');
//
$company->load('city', 'colors');
```

#### Autoloading with $with

Eloquent ships with a `$with` which accepts an array of relationships that should be eager loaded. We can use it as well:

```php
class Company extends Model
{
    use Devio\Eavquent\EntityAttributeValues;

    // Eager loading all the registered attributes
    protected $with = ['eav']; 
    // Or just load a few of them
    protected $with = ['city', 'colors'];
}
```
