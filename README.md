# TextLocal

A simple PHP API extension for Textlocal (any country but defaults to UK) SMS gateway integration in Laravel

# Installation

add this on your composer.json

```
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ridown/TextLocal.git"
        }
 ],
```

Require this package in your composer.json and update composer. This will download the package.

    composer require ridown/textlocal

or..

add the following to your require section of package.json

   "ridown/textlocal": "*",


## Laravel
After updating composer, add the ServiceProvider to the providers array in config/app.php

    Ridown\TextLocal\ServiceProvider::class,

N.B. package auto discovery available for laravel version 5.5 or later

After adding ServiceProvider, Run the command

    php artisan vendor:publish
    
After that set your TextLocal URL and auth Key inside config/sms.php

    $sms = new TextLocal();
    
    Send text message:
    $sms->send('message', 'mobile number');
    
    Get balance:
    $sms->getBalance();

    Get text message status:
    $sms->getMessageStatus($messageid);
