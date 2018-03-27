# TextLocal

A simple PHP API extension for Textlocal (any country but defaults to UK) SMS gateway integration in Laravel

# Installation

add this on your composer.json

```
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ridown/TextLocal"
        }
 ],
```

Require this package in your composer.json and update composer. This will download the package.

    composer require ridown/TextLocal




## Laravel
After updating composer, add the ServiceProvider to the providers array in config/app.php

    Ridown\TextLocal\ServiceProvider::class,

N.B. package auto discovery available for laravel version 5.5 or later

After adding ServiceProvider, Run the command

    php artisan vendor:publish
    
After that set your TextLocal URL and auth Key inside config/sms.php

$sms = new TextLocal();
$sms->send('message', 'mobile number');
