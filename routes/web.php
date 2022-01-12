<?php

$BASECONTROLLER = 'Web\FamilyHeadController';
$PREFIX = 'web.head.member.';

Route::get('/',function(){
	return view('welcome');
});

Route::resource('head', $BASECONTROLLER, ['as' => 'web']);
Route::get('/member/details/{id}', $BASECONTROLLER.'@showFamilyMemberDetails')->name($PREFIX.'show');
Route::get('/member/create', $BASECONTROLLER.'@createFamilyMember')->name($PREFIX.'create');
Route::post('/member/store', $BASECONTROLLER.'@storeFamilyMember')->name($PREFIX.'store');
Route::post('/get-state-cities', $BASECONTROLLER.'@getStateCities');