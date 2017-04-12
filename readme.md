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

#### JS counterpart for Real-Time Messaging

For real-time messaging you must install the JS counterpart via `npm` or `yarn`:

```
yarn add mardin
```

After adding the module via npm you may use as follows:
```js
// import mardin for use
import Mardin from 'mardin';

// ...

// initialize
let messenger = new Mardin(app);
```
**Ps.** `app` above refers to an instance of your client-side application and is optional.

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