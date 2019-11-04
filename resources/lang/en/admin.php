<?php 

return [

	/*--------------------------------------
	|	Common
	-------------------------------------------------------------------*/

		// ERROR MESSAGES
		'ERR_SOMETHING_WRONG' 	=> "Something went wrong on server.Please contact to Server",

	/*--------------------------------------
	|	Module name LOGIN
	-------------------------------------------------------------------*/

		// ERROR MESSAGES
				
		'ERR_USERNAME_REQUIRED' 	=> "Username field is required.",
		'ERR_EMAIL_REQUIRED' 		=> "Email field is required.",
		'ERR_PASSWORD_REQUIRED' 	=> "Password field is required.",

		'TITLE_USERNAME' 		=> 'Username',
		'TITLE_PASSWORD' 		=> 'Password',
		'TITLE_REMEMBER_ME' 	=> 'Remember me',
		'TITLE_FORGOT_PASSWORD' => 'Forgot Password',

		'BUTTON_LOGIN' 			=> 'Login',


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
		'TITLE_SUBMIT_CHANGES_BUTTON'=> 'Save Changes',
		'TITLE_ADD_USER_BUTTON'=> 'Add New User',

		// Response status
		'RESP_SUCCESS' 	=> 'success',
		'RESP_ERROR' 	=> 'error',
		'RESP_WARNING' 	=> 'warning',
		'RESP_INFO' 	=> 'info',

		// Response messages
		'USER_CREATED' 	=> 'User created successfully.',
		'USER_UPDATED' 	=> 'User updated successfully.',
		'USER_DELETED' 	=> 'User deleted successfully.',
		'USER_STATUS_CHANGED' => 'User status changed successfully.',

		// Response error
		'FAIL_USER_CREATE' 	=> 'Failed to create user, Something went wrong on server.',
		'FAIL_USER_UPDATE' 	=> 'Failed to update user, Something went wrong on server.',
		'FAIL_USER_DELETE' 	=> 'Failed to delete user, Something went wrong on server.',
		'FAIL_USER_STATUS_CHANGE' => 'Failed to change user status, Something went wrong on server.',

	
	/*--------------------------------------
	|	Module name Offence
	-------------------------------------------------------------------*/

		// ERROR MESSAGES
				
		'ERR_OFFENCE_NAME' 	=> "Name field is required.",
		'ERR_OFFENCE_DESC' 	=> "Description field is required",
		'ERR_OFFENCE_PENALTY' 	=> "Penalty field is required.",
		'ERR_OFFENCE_PENALTY_FORMAT' => "Please enter penalty in valid format.",


];