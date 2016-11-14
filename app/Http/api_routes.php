<?php
	
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

	$api->post('auth/login', 'App\Api\V1\Controllers\AuthController@login');

    $api->group(['prefix' => 'admin','middleware'=>['api.auth','admin']], function ($api){

        $api->get('users', 'App\Api\V1\Controllers\UserController@index'); // solo masajistas

        $api->get('users/{id}', 'App\Api\V1\Controllers\UserController@show')->where('id', '[0-9]+');
        $api->post('users', 'App\Api\V1\Controllers\AuthController@createUser'); // solo masajistas
        $api->put('users/{id}', 'App\Api\V1\Controllers\UserController@update')->where('id', '[0-9]+');
        $api->delete('users/{id}', 'App\Api\V1\Controllers\UserController@destroy')->where('id', '[0-9]+');



        $api->get('users/{user_id}/emails', 'App\Api\V1\Controllers\EmailController@getUserEmails')->where(['user_id' => '[0-9]+']);
        $api->get('users/emails/{email_id}', 'App\Api\V1\Controllers\EmailController@showEmail')->where(['email_id' => '[0-9]+']);
        $api->post('users/{user_id}/emails', 'App\Api\V1\Controllers\EmailController@store')->where(['user_id' => '[0-9]+']);
        $api->put('users/{user_id}/emails/{email_id}', 'App\Api\V1\Controllers\EmailController@update')->where(['user_id' => '[0-9]+', 'email_id' => '[0-9]+']);
        $api->delete('users/emails/{email_id}', 'App\Api\V1\Controllers\EmailController@destroy')->where(['email_id' => '[0-9]+']);

        $api->get('users/{user_id}/phones', 'App\Api\V1\Controllers\PhoneController@getUserPhones')->where(['user_id' => '[0-9]+']);
        $api->get('users/phones/{phone_id}', 'App\Api\V1\Controllers\PhoneController@showPhone')->where(['phone_id' => '[0-9]+']);
        $api->post('users/{user_id}/phones', 'App\Api\V1\Controllers\PhoneController@store')->where('user_id', '[0-9]+');
        $api->put('users/phones/{phone_id}', 'App\Api\V1\Controllers\PhoneController@update')->where(['phone_id' => '[0-9]+']);
        $api->delete('users/phones/{phone_id}', 'App\Api\V1\Controllers\PhoneController@destroy')->where(['phone_id' => '[0-9]+']);

        $api->get('users/{user_id}/addresses', 'App\Api\V1\Controllers\AddressControll@getUserAddresses')->where(['user_id' => '[0-9]+']);
        $api->get('users/addresses/{address_id}', 'App\Api\V1\Controllers\AddressControll@showAddress')->where(['user_id' => '[0-9]+','address_id' => '[0-9]+']);
        $api->post('users/{user_id}/addresses', 'App\Api\V1\Controllers\AddressControll@store')->where(['user_id' => '[0-9]+']);
        $api->put('users/addresses/{address_id}', 'App\Api\V1\Controllers\AddressControll@update')->where(['address_id' => '[0-9]+']);
        $api->delete('users/addresses/{address_id}', 'App\Api\V1\Controllers\AddressControll@destroy')->where(['address_id' => '[0-9]+']);

        $api->get('users/{user_id}/socials', 'App\Api\V1\Controllers\SocialController@getUserSocials')->where(['user_id' => '[0-9]+']);
        $api->get('users/{user_id}/socials/{social_id}', 'App\Api\V1\Controllers\SocialController@showSocial')->where(['social_id' => '[0-9]+']);
        $api->post('users/{user_id}/socials', 'App\Api\V1\Controllers\SocialController@store')->where(['user_id' => '[0-9]+']);
        $api->put('users/socials/{social_id}', 'App\Api\V1\Controllers\SocialController@update')->where(['social_id' => '[0-9]+']);
        $api->delete('users/socials/{social_id}', 'App\Api\V1\Controllers\SocialController@destroy')->where(['social_id' => '[0-9]+']);









//        $api->get('users/{id}/emails', function ($id){
//            $emails = \Illuminate\Support\Facades\DB::table('emails')->where('user_id', $id)->select('email')->get();
//            return $emails;
//        })->where('id', '[0-9]+');
//
//
//        // the following end points needs more test
//        $api->post('users/recovery', 'App\Api\V1\Controllers\AuthController@recovery');
//        $api->post('users/reset', 'App\Api\V1\Controllers\AuthController@reset');
//




    });



    // example of protected route
//    $api->get('protected', ['middleware' => ['admin'], function () {
//        return \App\User::all();
//    }]);
//
//    // example of free route
//    $api->get('free', function() {
//        return \App\User::all();
//    });

});
