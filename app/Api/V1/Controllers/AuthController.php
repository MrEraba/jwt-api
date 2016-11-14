<?php

namespace App\Api\V1\Controllers;

use App\Address;
use App\Phone;
use JWTAuth;
use Validator;
use Config;
use App\User;
use App\Email;

use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Exception\ValidationHttpException;

class AuthController extends Controller
{
    use Helpers;

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        $validator = Validator::make($credentials, [
            'email' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->response->errorUnauthorized();
            }
        } catch (JWTException $e) {
            return $this->response->error('could_not_create_token', 500);
        }

        return response()->json(compact('token'));
    }

    public function createUser(Request $request)
    {
        $signupFields = Config::get('boilerplate.signup_fields');
        $hasToReleaseToken = Config::get('boilerplate.signup_token_release');

        $userData = $request->only($signupFields);

        $validator = Validator::make($userData, Config::get('boilerplate.signup_fields_rules'));

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        User::unguard();
        $user = User::create($userData);
        User::reguard();

        if($request->has('emails')){

            $emails = $request->emails;
            $to_save =[];

            foreach ($emails as $email) {

                $email_values = $email['email'];
                $validator_email = Validator::make($email_values, ['email' => 'required|email|unique:users|unique:emails',]);

                if ($validator_email->fails()){
                    throw new ValidationHttpException($validator_email->errors()->all());
                } else {
                    array_push($to_save, new Email($email_values));
                }
            }
            $user->emails()->saveMany($to_save);
        }

        if($request->has('phones')){

            $phones = $request->phones;
            $to_save =[];

            foreach ($phones as $phone) {
                $phone_values = ['number' => $phone['number'], 'description' => $phone['description'], 'main' => $phone['main']];

                $validator_phone = Validator::make($phone_values, ['number' => ['regex:/^[0-9]{11}$/','required','unique:phones'], 'description' => 'required|min:4|max:30', 'main' => 'boolean']);

                if ($validator_phone->fails()){
                    throw new ValidationHttpException($validator_phone->errors()->all());
                } else {
                    array_push($to_save, new Phone($phone_values));
                }
            }
            $user->phones()->saveMany($to_save);
        }

        if($request->has('addresses')){

            $addresses = $request->addresses;
            $to_save =[];

            foreach ($addresses as $address) {

                $address_values = ['address' => $address['address'], 'main' => $address['main']];

                $validator_address = Validator::make($address_values, ['address' => 'required|min:5|unique:addresses', 'main' => 'boolean']);

                if ($validator_address->fails()){
                    throw new ValidationHttpException($validator_address->errors()->all());
                } else {
                    array_push($to_save, new Address($address_values));
                }
            }
            $user->addresses()->saveMany($to_save);
        }

        if($request->has('socials')){

            $socials = $request->socials;
            $to_save =[];

            foreach ($socials as $social) {

                $social_values = ['uri' => $social['uri'], 'aplication' => $social['aplication']];

                $validator_social = Validator::make($social_values, ['uri' => 'required|min:5|unique:socials', 'aplication' => 'required|min:5']);

                if ($validator_social->fails()){
                    throw new ValidationHttpException($validator_social->errors()->all());
                } else {
                    array_push($to_save, new Address($social_values));
                }
            }
            $user->socials()->saveMany($to_save);
        }

        if(!$user->id) {

            return $this->response->error('could_not_create_user', 500);
        }

        if($hasToReleaseToken) {
            //return $this->login($request);
            return $this->response->array($user)->setStatusCode(201);
        }

        return $this->response->created();

    }

    public function recovery(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required'
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject(Config::get('boilerplate.recovery_email_subject'));
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return $this->response->noContent();
            case Password::INVALID_USER:
                return $this->response->errorNotFound();
        }
    }

    public function reset(Request $request)
    {
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $validator = Validator::make($credentials, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }
        
        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                if(Config::get('boilerplate.reset_token_release')) {
                    return $this->login($request);
                }
                return $this->response->noContent();

            default:
                return $this->response->error('could_not_reset_password', 500);
        }
    }


}