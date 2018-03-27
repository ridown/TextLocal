<?php

return array(

	/*
	 * Settings
	 */
    'default' => env('SMS_DRIVER', 'TextLocal'),
    'connections' => [
        'TextLocal' => [
            'key'       => env('SMS_TEXTLOCAL_KEY', ''),
            'sender'    => env('SMS_TEXTLOCAL_NAME',''),
            'username'  => env('SMS_TEXTLOCAL_USERNAME',''),
            'hash'      => env('SMS_TEXTLOCAL_HASH',''),
            'url'       => env('SMS_TEXTLOCAL_URL', 'http://api.txtlocal.com/'),
            'format'    => env('SMS_TEXTLOCAL_FORMAT', 'xml'),
        ]
    ]
    
);
