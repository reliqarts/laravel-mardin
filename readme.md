# Mardin Messenger

Mardin is a messaging package for Laravel 5.x based on Laravel Messenger.

[![Built For Laravel](https://img.shields.io/badge/built%20for-laravel-red.svg?style=flat-square)](http://laravel.com)
![Build Status](https://img.shields.io/circleci/project/reliqarts/mardin.svg?style=flat-square)
[![StyleCI](https://styleci.io/repos/71434979/shield?branch=master)](https://styleci.io/repos/71434979)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/reliqarts/mardin.svg?style=flat-square)](https://scrutinizer-ci.com/g/reliqarts/mardin/)
[![License](https://poser.pugx.org/reliqarts/mardin/license?format=flat-square)](https://packagist.org/packages/reliqarts/mardin)
[![Latest Stable Version](https://poser.pugx.org/reliqarts/mardin/version?format=flat-square)](https://packagist.org/packages/reliqarts/mardin)
[![Latest Unstable Version](https://poser.pugx.org/reliqarts/mardin/v/unstable?format=flat-square)](//packagist.org/packages/reliqarts/mardin)

&nbsp;


## Key Features

Mardin can be integrated seemlessly with your existing application.

### Guided Routes

The package provides routes for generating resized/cropped/dummy images. 
- Routes are configurable you you may set any middleware and prefix you want.
- Generated images are *cached to disk* to avoid regenerating frequently accessed images and reduce overhead.

### Image file reuse

For situations where different instances of models use the same image.
- The package provides a safe removal feature which allows images to be detached and only deleted from disk if not being used elsewhere.
- An overridable method used to determine when an image should be considered *safe* to delete. 

## Installation & Usage

### Installation

Install via composer; in console: 
```
composer require reliqarts/mardin
``` 
or require in *composer.json*:
```js
{
    "require": {
        "reliqarts/mardin": "*"
    }
}
```
then run `composer update` in your terminal to pull it in.

Once this has finished, you will need to add the service provider to the providers array in your app.php config as follows:

```php
ReliQArts\Mardin\MardinServiceProvider::class,
```

Finally, publish package resources and configuration:

```
php artisan vendor:publish --provider="ReliQArts\Mardin\MardinServiceProvider"
``` 

You may opt to publish only configuration by using the `config` tag:

```
php artisan vendor:publish --provider="ReliQArts\Mardin\MardinServiceProvider" --tag="config"
``` 
You may publish migrations in a similar manner using the tag `migrations`.

### Setup

Set the desired environment variables so the package knows your user model, transformer, etc. 

Example environment config:
```
MARDIN_USER_MODEL="App\\User"
MARDIN_USER_TRANSFORMER="App\\Transformers\\UserTransformer"
```

These variables, and more are explained within the [config](https://github.com/ReliQArts/mardin/blob/master/config/mardin.php) file.

And... it's ready! :ok_hand:
### Usage

*coming soon...*