# Twilio notifications channel for Laravel 5.3 [WIP]

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/twilio.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/twilio)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/twilio/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/twilio)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/twilio.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/twilio)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/twilio.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/twilio)

This package makes it easy to send [Twilio notifications](https://documentation.twilio.com/docs) with Laravel 5.3.

## Installation

You can install the package via composer:

``` bash
composer require laravel-notification-channels/twilio
```

You must install the service provider:

```php
// config/app.php
'providers' => [
    ...
    NotificationChannels\TwilioNotifications\Provider::class,
];
```

## Usage

Now you can use the channel in your `via()` method inside the notification:

``` php
use NotificationChannels\TwilioNotifications\Channel;
use NotificationChannels\TwilioNotifications\SmsMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [Channel::class];
    }

    public function toTwilio($notifiable)
    {
        return (new SmsMessage())
            ->content("Your {$notifiable->service} account was approved!");
    }
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing
    
``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email m.pociot@gmail.com instead of using the issue tracker.

## Credits

- [Gregorio Hernández Caso](https://github.com/gregoriohc)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
