<?php
return [
	
	/*--------------------------------------------------------
	|  General Constants
	------------------------------------*/
		// 'ADMINEMAIL' 		=> 'naik.orchid@gmail.com',
		'ADMINFROMNAME'     => 'ADMIN',
		'SUPERADMINROLENAME' => 'super-admin',
		'SITENAME'     => 'Orchid Store',

		//Test Mail
		'ADMINEMAIL' 		=> 'eluminous_se42@eluminoustechnologies.com',
		'COMPANYURL' 		=> 'http://dev.eluminousdev.com/orchid/',
		// 'ACCOUNTEMAIL' 		=> 'eluminous.se47@gmail.com',


	/*--------------------------------------------------------
	|  SMS GATEWAY
	------------------------------------*/
		// 'SMS_URL' 	   => 'http://sms.edusaral.in/pushsms.php',
		// 'SMS_USERNAME' => 'eluminous',
		// 'SMS_PASSWORD' => 'admin123',
		// 'SMS_SENDERID' => 'ORCHID',
		
	/*--------------------------------------------------------
	|  API CONSTANTS
	------------------------------------*/
		'APP_ADMIN' => env('APP_ADMIN', 'admin'),
    	'APP_TOKEN' => env('APP_TOKEN', 'admin123456'),
    	'API_URL' => env('APP_URL', 'http://localhost:81/orchid-store').'/api/v1/',
    	'HTTP_UNAUTHORIZED' => '401',
	    'SUCCESS' => '200',
	    'UNSUCCESS' => '404',	    
	    'STATUS_SUCCESS' => 'Success',
	    'STATUS_UNSUCCESS' => 'Failed',	 
		'OTP_VALID_MINS' => 15,
		'STATUS_ACTIVE'=>1,
    	'STATUS_INACTIVE'=>0,
    	'API_PAGE_LIMIT'=>10,

];

?>