<?php

return array(

	/*
	 * Settings
	 */
	'default' => env('SMS_DRIVER', 'TextLocal'),
    	'connections' => [
      		'TextLocal' => [
        		'key'    => env('SMS_TEXTLOCAL_KEY', ''),
		    	'sender' => env('SMS_TEXTLOCAL_NAME',''),
			'url'    => env('SMS_TEXTLOCAL_URL', 'http://api.txtlocal.com/send/'),
			'format' => env('SMS_TEXTLOCAL_FORMAT', 'xml'),
		  ]
    	]
);
