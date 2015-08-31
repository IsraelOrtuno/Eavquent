## Propertier - EAV modeling for Eloquent

[![Build Status](https://travis-ci.org/IsraelOrtuno/Propertier.svg?branch=master)](https://travis-ci.org/IsraelOrtuno/Propertier)

An EAV modeling package for Eloquent. Handle dynamic attributes as if you were using a regular Eloquent model.

### Introduction

As Wikipedia describes...

> [EAV (Entity Attribute Value)][1] is a data model to describe entities where the number of attributes that can be used to describe them is potentially vast.

An EAV architecture might be useful when your entities attributes might change in future or may be personalized by the user.

This system may affect performance as it is handling the database relations manually and will perform more SQL queries for inserting just a simple field than if it were a regular field.

Before using in large systems, consider performance vs flexibility.

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

### Usage

#### Registering a new property

#### Setting a property value

#### Setting multivalue property values

#### Deleting an existing property

#### Adding/modifying property types



#### Important

Do not use the package with eager loading, it already uses it internally. Adding the `properties` relation to a `$with` variable or `with()` method might cause unexpected behaviour.


### TO-DO

- [ ] Clear any null property value to make the database lighter.
- [ ] Create a queue for insert/update queries to rum then in a row.
- [ ] Chance of caching properties related to an entity.

[1]: https://en.wikipedia.org/wiki/Entity%E2%80%93attribute%E2%80%93value_model
