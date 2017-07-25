# Mardin Messenger

Mardin is a messaging package for Laravel 5.x based on Laravel Messenger.

[![Built For Laravel](https://img.shields.io/badge/built%20for-laravel-red.svg?style=flat-square)](http://laravel.com)
![Build Status](https://img.shields.io/circleci/project/reliqarts/mardin.svg?style=flat-square)
[![StyleCI](https://styleci.io/repos/88008918/shield?branch=master)](https://styleci.io/repos/88008918)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/reliqarts/mardin.svg?style=flat-square)](https://scrutinizer-ci.com/g/reliqarts/mardin/)
[![License](https://poser.pugx.org/reliqarts/mardin/license?format=flat-square)](https://packagist.org/packages/reliqarts/mardin)
[![Latest Stable Version](https://poser.pugx.org/reliqarts/mardin/version?format=flat-square)](https://packagist.org/packages/reliqarts/mardin)
[![Latest Unstable Version](https://poser.pugx.org/reliqarts/mardin/v/unstable?format=flat-square)](//packagist.org/packages/reliqarts/mardin)

&nbsp;


## Key Features

- Integrates seemlessly with your existing application.
- Supports multi-participant conversations (threads)
- Multiple conversations per user
- View the last message for each thread available
- Easily fetch all messages in the system, all messages associated to the user, or all message associated to the user with new/unread messages
- Fetch users unread message count easily
- Very flexible usage so you can implement your own access control
- Supports real-time messaging via integration with Laravel Echo

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

Publish package resources and configuration:

```
php artisan vendor:publish --provider="ReliQArts\Mardin\MardinServiceProvider"
``` 

You may opt to publish only configuration by using the `config` tag:

```
php artisan vendor:publish --provider="ReliQArts\Mardin\MardinServiceProvider" --tag="config"
``` 

Create a `users` table if you do not have one already.

**(Optional)** Define names of database tables in *laravel messenger*'s config file (config/laravel-messenger) if you don't want to use default ones:

```
'messages_table' => 'messenger_messages',
'participants_table' => 'messenger_participants',
'threads_table' => 'messenger_threads',
```

*See:* Laravel messenger [readme](https://github.com/cmgmyr/laravel-messenger/blob/master/readme.md) for more information on Laravel Messenger setup.

Run the migrations to create `messages`, `threads`, and `participant` tables.

```
php artisan migrate
``` 

#### Configuration

Set the desired environment variables so the package knows your user model, transformer, etc. 

Example environment config:
```
MARDIN_USER_MODEL="App\\User"
MARDIN_USER_TRANSFORMER="App\\Transformers\\UserTransformer"
```

These variables, and more are explained within the [config](https://github.com/ReliQArts/mardin/blob/master/config/mardin.php) file.

#### Traits & Contracts

You must ensure that your user model and user transformer classes are properly set in your configuration (as shown above) and that they implement the `ReliQArts\Mardin\Contracts\User` and `ReliQArts\Mardin\Contracts\UserTransformer` contracts respectively.

Your `User` model must also use the `Messagable` trait.

e.g. User model:

```php
// ...
use ReliQArts\Mardin\Traits\Messagable;
use ReliQArts\Mardin\Contracts\User as MardinUserContract;

class User extends Authenticatable implements MardinUserContract {
    use Messagable;

    // ...
}
```

You may also extend the Message, Participant, and Thread models. Extending the `Message` model is encouraged since you may very well wish to add a specific policy for security (via [Laravel Guard](https://laravel.com/docs/5.4/authentication)).

e.g. Message model:

```php
use ReliQArts\Mardin\Models\Message as MardinMessage;

class Message extends MardinMessage
{
    // ...
}
```

e.g. Policy Implementation in `app\Providers\AuthServiceProvider.php`

```php
use App\Message;
use ReliQArts\Mardin\Policies\MessagePolicy;

// ...

/**
* The policy mappings for the application.
*
* @var array
*/
protected $policies = [
    Message::class => MessagePolicy::class,
    
    // ...
];

// ...
```

#### Real-Time Messaging

For real-time messaging you must install the JS counterpart via `npm` or `yarn`:

```
yarn add mardin
```

After adding the module via npm you may use as follows:
```js
// import mardin for use
import Mardin from 'mardin';

// initialize
let messenger = new Mardin(app);
```
*Note:* `app` above refers to an instance of your client-side application and is optional.

And... it's ready! :ok_hand:


### Usage

#### Routes

The following routes are made available. For clarification you may refer to the method documentations in `ReliQArts\Mardin\Http\Controllers\MessagesController` [here](https://github.com/reliqarts/mardin/blob/master/src/Http/Controllers/MessagesController.php).

```
|        | POST                           | messages                                                           | store-message                               | ReliQArts\Mardin\Http\Controllers\MessagesController@store                           | web                                                 |
|        | GET|HEAD                       | messages/c/unread                                                  | unread-messages-count                       | ReliQArts\Mardin\Http\Controllers\MessagesController@unreadCount                     | web                                                 |
|        | POST                           | messages/del                                                       | unread-message                              | ReliQArts\Mardin\Http\Controllers\MessagesController@delete                          | web                                                 |
|        | GET|HEAD                       | messages/in/{filter}.json                                          | in-threads                                  | ReliQArts\Mardin\Http\Controllers\MessagesController@inboxData                       | web                                                 |
|        | POST                           | messages/m/new                                                     | create-message                              | ReliQArts\Mardin\Http\Controllers\MessagesController@create                          | web                                                 |
|        | POST                           | messages/mr                                                        | read-message                                | ReliQArts\Mardin\Http\Controllers\MessagesController@read                            | web                                                 |
|        | POST                           | messages/mur                                                       | unread-message                              | ReliQArts\Mardin\Http\Controllers\MessagesController@unread                          | web                                                 |
|        | GET|HEAD                       | messages/t/{thread}/messages.json                                  | in-thread-messages                          | ReliQArts\Mardin\Http\Controllers\MessagesController@threadMessagesData              | web                                                 |
|        | POST                           | messages/u/{thread}                                                | update-message                              | ReliQArts\Mardin\Http\Controllers\MessagesController@update                          | web                                                 |
|        | GET|HEAD                       | messages/view/{thread}                                             | show-message                                | ReliQArts\Mardin\Http\Controllers\MessagesController@show                            | web                                                 |
|        | GET|HEAD                       | messages/{type?}                                                   | messages       
```


#### Authorization

Mardin supports Laravel's default authorization model. To use the provided policy, map the policy in your `AuthServiceProvider` like so:

```php
use App\Message; // a custom message model that extends ReliQArts\Mardin\Models\Message
use ReliQArts\Mardin\Policies\MessagePolicy;

/**
 * The policy mappings for the application.
 *
 * @var array
 */
protected $policies = [
    // ...
    Message::class => MessagePolicy::class,
];
```

The policy uses the `canSendMardinMessage()` and `canReceiveMardinMessage()` methods implemented on the `User` model. These methods are enforced by `ReliQArts\Mardin\Contracts\User`.

#### Sending a Message

Start a new thread by making a request to `messages/m/new` (POST).

##### Sample New Message Form

```php
{!! Form::open(['route' => 'create-message']) !!}
{!! Form::hidden('subject', "New Message") !!}
{!! Form::hidden('recipients[]', $user->id) !!}
<button class="btn new-message flat" title="Send a message to {{$user->name}}.">
    <span class="icon icon-email icon-lg"></span>
    <span>Send Message</span>
</button>
{!! Form::close() !!}
```

The above example uses `laravelcollective/html` to generate a HTML form which posts to the `create-message` route.


---
For more information on Laravel Messenger, check it out [here](https://github.com/cmgmyr/laravel-messenger).

:beers: cheers!