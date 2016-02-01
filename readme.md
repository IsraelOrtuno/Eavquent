Propertier - EAV modeling for Eloquent
======================================
[![Build Status](https://travis-ci.org/IsraelOrtuno/Propertier.svg?branch=master)](https://travis-ci.org/IsraelOrtuno/Propertier)

This package will help you to provide an EAV structure and functionality to your Eloquent models.

## Install

#### 1. Require with composer
Require this package using composer:

```
composer require devio/propertier
```

#### 2. Load the Service Provider
Add the `PropertierServiceProvider` class to your `providers` array in `config/app.php`:

```
Devio\Propertier\PropertierServiceProvider::class,
```

#### 3. Publish and migrate
This package needs a couple of tables for creating the EAV schema structure. First we will publish the package migrations:

```
php artisan vendor:publish --provider="Devio\Propertier\PropertierServiceProvider" --tag="migrations"
```

Then just migrate:

```
php artisan migrate
```

#### 4. Publish the package configuration (optional)
Publish the package configuration

```
php artisan vendor:publish --provider="Devio\Propertier\PropertierServiceProvider" --tag="config"
```

#### Current status

**28/12/2015** - First alpha will be tagged in a few days.
