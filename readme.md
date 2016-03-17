Eavquent - EAV modeling for Eloquent
======================================
[![Build Status](https://travis-ci.org/IsraelOrtuno/Eavquent.svg?branch=master)](https://travis-ci.org/IsraelOrtuno/Eavquent)

This package will help you to provide an EAV structure and functionality to your Eloquent models.

- [Introduction](#introduction)
  - [Basics](#basics)
  - [Performance](#performance)
- [Install](#install)
  - [Laravel set up](#laravel-setup)
  - [Framework agnositc set up](#framework-agnostic)
- [Entities](#entities)
  - [Configuring the Eloquent model](#configuring-eloquent)
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

<a name="basics"></a>
### Basics

#### Entity

An entity represents a real element which needs to extend its attributes dynamically. Example: elemenents such as `Product`, `Customer` or `Sale` are likely to be entities.

In this case an entity will be represented by an Eloquent model.

#### Attribute

The attribute act as the "column" we would like to add to an entity. An attribute gets a name such as `price`, `city` or `colors` to get identified and will be linked to an entity object. It will also play very closely with a data type instance which will cast or format its value when writing or reading from database.

This element will also be responsible of defining some default behaviour like data validation or default values.

#### Value

This parameter is responsible of storing data values related to a certain attribute and to a particular entity instance (row). 

In Eavquent implementation, a Value instance will represent the content of an attribute related to a particular entity instance. It will contain the real value of the `price` attribute we have registered for a `product[id=1]` entity.

Values are stored in different tables based on their data type. String values will be stored in a table called (by default) `eav_values_varchar` when integer values would use `eav_values_integer` instead. Both tables columns are identical except the data type of the `content` column which is adapted to the data type they store.

<a name="performance"></a>
### The performance loss

EAV modeling is known for its lack of performance. It is also known for its complexity in terms of querying data if compared with the cost of querying any other horizontal structure. This paradigm has been tagged as anti-pattern in many articles and there is a lot of polemic about whether it should be used.

Since we are storing our entity, attribute and value in different tables, it's required to perform multiple queries to perform any operation. This means if we have 4 attributes registered for an entity, the package will perform at least 5 queries:

```php
select * from `companies`
select * from `eav_values_varchar` where `attribute_id` = '1' and `eav_values_varchar`.`entity_id` in ('1', '2', '3', '4', '5') and `eav_values_varchar`.`entity_type` = 'App\Company'
select * from `eav_values_varchar` where `attribute_id` = '2' and `eav_values_varchar`.`entity_id` in ('1', '2', '3', '4', '5') and `eav_values_varchar`.`entity_type` = 'App\Company'
select * from `eav_values_varchar` where `attribute_id` = '3' and `eav_values_varchar`.`entity_id` in ('1', '2', '3', '4', '5') and `eav_values_varchar`.`entity_type` = 'App\Company'
select * from `eav_values_varchar` where `attribute_id` = '4' and `eav_values_varchar`.`entity_id` in ('1', '2', '3', '4', '5') and `eav_values_varchar`.`entity_type` = 'App\Company'
```

### The flexibility win

However, despite the performance issues, EAV provides a very high flexibility. It let us have dynamic attributes that can be added / removed at any time without afecting database structure. It also helps when working with columns that will mainly store `NULL` values.

Considering the user accepts the lack of performance EAV comes with, the package has been developed with flexibility in mind so at least the user can fight that performance issue. Performance could be improved by loading all the entity related values in a single query and letting a bit of PHP logic organize them into relationships but decided not to, in favour of making database querying more flexible. 

As explained below, this package loads the entity values as if they were custom Eloquent relationships. Is for this reason we can easily query through them as if they were a regular Eloquent relation.

Loading values as relationships will let us load only those values we may require for a certain situation, leaving some others just unloaded. It will also let us make use of the powerful Eloquent tools for querying relations so we could easily filter the entities we are fetching from database based on conditions we will directly apply to the values content.

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
