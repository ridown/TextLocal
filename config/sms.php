<?php

	return array(

		/*
		|--------------------------------------------------------------------------
		| Settings
		|--------------------------------------------------------------------------
		*/
		'default' => env('SMS_DRIVER', 'TextLocal'),
    
    'connections' => [
      'TextLocal' => [
		    'url'    => env('SMS_TEXTLOCAL_URL', 'http://api.txtlocal.com/send/'),
        'key'    => env('SMS_TEXTLOCAL_KEY', ''),
		    'sender' => env('SMS_TEXTLOCAL_NAME',''),
		  ]
    ]
	);
