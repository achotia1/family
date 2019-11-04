<?php 

return [

	/*--------------------------------------
	|	Module name USERS
	-------------------------------------------------------------------*/

		// Errors
		'ERR_FIRST_NAME' 	=> "First name field is required.",
		'ERR_LAST_NAME' 	=> "Last name field is required.",
		'ERR_EMAIL_NAME' 	=> "Email field is required.",
		'ERR_EMAIL_FORMAT' 	=> "Email format is invalid.",
		'ERR_EMAIL_DUP' 	=> "Email has already taken.",
		'ERR_PASS' 			=> "Password field is required.",
		'ERR_PASS_MIN_SIZE' => "Password should be minimum 6 character long.",
		'ERR_CONFIRM_PASS' 	=> "Confirm password field is required.",
		'ERR_COMPARE_PASS' 	=> "Confirm password not match with password.",
		'ERR_ROLE' 			=> "Role field is required.",

		// Titles
		'TITLE_FIRST_NAME'  => 'First Name',
		'TITLE_LAST_NAME'  	=> 'Last Name',
		'TITLE_EMAIL'  		=> 'Email Address',
		'TITLE_PASS'  		=> 'Password',
		'TITLE_CONFIRM_PASS'=> 'Confirm Password',
		'TITLE_SELECT_ROLE'	=> 'Select Role',
		'TITLE_SUBMIT_BUTTON'=> 'Save',
		'TITLE_ADD_USER_BUTTON'=> 'Add New User',

		// Response status
		'RESP_SUCCESS' 	=> 'success',
		'RESP_ERROR' 	=> 'error',
		'RESP_WARNING' 	=> 'warning',
		'RESP_INFO' 	=> 'info',

		// Response messages
		'USER_CREATED' 	=> 'User register successfully.',
		'USER_UPDATED' 	=> 'User updated successfully.',
		'USER_DELETED' 	=> 'User deleted successfully.',
		'USER_STATUS_CHANGED' => 'User status changed successfully.',

		// Response error
		'FAIL_USER_CREATE' 	=> 'Failed to register user, Something went wrong on server.',
		'FAIL_USER_UPDATE' 	=> 'Failed to update user, Something went wrong on server.',
		'FAIL_USER_DELETE' 	=> 'Failed to delete user, Something went wrong on server.',
		'FAIL_USER_STATUS_CHANGE' => 'Failed to change user status, Something went wrong on server.',

	/*--------------------------------------
	|	Module name CONTACTUS
	-------------------------------------------------------------------*/
		// Errors
		'ERR_CMESSAGE' 			=> "Message field is required.",
		'MAIL_STATUS_SUCCESS' 	=> "success",
		'MAIL_STATUS_FAILED' 	=> "failed",
		'MAIL_MSG' 				=> "Email Sent Successfully",

	/*--------------------------------------
	|	Module name Forgot Password AND Password Set
	-------------------------------------------------------------------*/
		// Errors
		'ERR_ACCOUNT_NOTFOUND' 	=> "Could not find an account with that email address.",
		'ERR_MAIL' 				=> "Error while sending email",
		'ERR_ADMIN' 			=> "Error Admin account not accessible",

		'PASS_SET_SUCCESS_MSG' 	=> "Password Reset Link Sent Successfully",
		'ERR_PASS_SET_SUCCESS' 	=> "Password Updated Successfully",
		'ERR_PASS_SET_FAIL' 	=> "Failed to reset password, Token expired",

	/*--------------------------------------
	|	Module name Join Faculty
	-------------------------------------------------------------------*/

		'ERR_FIRST_NAME_REGEX'       => 'First name should accept latter\'s and numbers only.',
        'ERR_LAST_NAME_REGEX'        => 'Last name should accept latter\'s and numbers only.',
        'ERR_PHONE_REGEX'          	 => 'Phone field format is invalid.',
        'ERR_PHONE_REQUIRED'      	 => 'Phone field is required.',
        'ERR_FIRM_NAME_REQUIRED'     => 'Firm name field is required.',
        'ERR_PRACTICE_AREA___NAME'   => 'Practice area field is required.',	
  //       'ERR_WEBSITE_REQUIRED'       => 'Website field is required.',
  //       'ERR_WEBSITE_URL'            => 'Website URL format is invalid.',
  //       'ERR_ABOUT_LECTURE_REQUIRED' => 'About lecture field is required.',
		

];