Propertier - EAV modeling for Eloquent
======================================
[![Build Status](https://travis-ci.org/IsraelOrtuno/Propertier.svg?branch=master)](https://travis-ci.org/IsraelOrtuno/Propertier)

An EAV modeling package for `Eloquent`. Handle dynamic attributes as if you were using a regular `Eloquent` model.

## Introduction

As Wikipedia describes...

> [EAV (Entity Attribute Value)][1] is a data model to describe entities where the number of attributes that can be used to describe them is potentially vast.

An EAV architecture might be useful when your entities attributes might change in future or may be personalized by the user.

This system may affect performance as it is handling the database relations manually and will perform more SQL queries for inserting just a simple field than if it were a regular field.

Before using in large systems, consider performance vs flexibility.

## Installation

### 1. Require the package with composer

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

### 2. Add the Service Provider

Once installed, you should include the `PropertierServiceProvider` to the providers array in `config/app.php`.

```
'providers' => [
  ...
  Devio\Propertier\PropertierServiceProvider::class,
  ...
]
```

### 3. Publish the package assets

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

## Getting Started

`Propertier` works straight over an `Eloquent` model but without changing its base functionality. It is just a `Trait` which should be used when defining your `Eloquent` model.

Since `Propertier` uses polimorpohic relatinoships to maintain its structure, it is recommended to set our model property `$morphClass` to something simpler than its default value which would be the full namespaced class name. This way, our database polimorphic relations will use this shorter name instead. This will save us some bytes and help database readability.

```php
use Devio\Propertier\PropertierTrait;
use Illuminate\Database\Model as Eloquent;

class User extends Eloquent {

    use PropertierTrait;

    // Optional
    protected $morphClass = 'User';

}
```

That's it! Let's register some properties and our model is ready to go! 

> **Note:** The `$morphClass` property must be a unique name. It will not work properly if more than a model has the same value here as it is used for instantiating the right model class for polymorphic relations.

> **Note:** Do not use the package with eager loading, it already uses it internally. Adding the `properties` relation to a `$with` variable or `with()` method might cause unexpected behaviour.

## Usage

### Registering a new property

### Setting a property value

Setting a property value will be as easy as setting any regular `Eloquent` attribute. If we have registered a property called `country`, only do:

```php
$user = User::find(1);
$user->country = 'Spain';
$user->save();
```

In the example above we are assuming that the entity already exists into the database, `Propertier` also works when creating fresh models:

```php
$user = new User(['name' => 'Joe');
$user->country = 'France';
$user->save();
```

That is good enough... But what if we pass the property value into a fillable method as `create()`, `fill()` or even the model constructor itself?

```php
$user = User::create([
    'name'      => 'Frank',
    'country'   => 'Italy'
);
```

It will work at any case. Now you can access the property value in two ways:

```php
echo $user->contry; // Spain
// or using the method
echo $user->getProperty('country');
```

### Setting multivalue property values

### Deleting an existing property

### Adding/modifying property types

### Eloquent overridden methods

Some `Eloquent` methods had to be overridden to completely integrate `Propertier` into `Eloquent` and be able to use it as a regular model.

* `isFillable()`
* `fillableFromAttributes()`
* `__get()`
* `__set()`

If you are overriding any of these method in your model, make sure to call the trait methods version. 

## TO-DO

- [ ] Clear any null property value to make the database lighter.
- [ ] Create a queue for insert/update queries to rum then in a row.
- [ ] Chance of caching properties related to an entity.

[1]: https://en.wikipedia.org/wiki/Entity%E2%80%93attribute%E2%80%93value_model
