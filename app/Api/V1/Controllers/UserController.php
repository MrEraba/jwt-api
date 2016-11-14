<?php

namespace App\Api\V1\Controllers;

use App\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Config;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\ValidationHttpException;
use Validator;
use JWTAuth;


class UserController extends Controller
{
    use Helpers;

    public function index(){

        $users = User::all()->sortByDesc('created_at');

        return $this->response->array($users)->setStatusCode(200);
    }

    public function show($id){
        $user = User::findOrFail($id);

        if(!$user){
            return $this->response()->error('resource_not_found', 404);
        } else {

            $emails = $user->emails;
            $phones = $user->phones;

            return $this->response->array($user)->setStatusCode(200);
        }
    }

    public function update(Request $request, $id){
        $user = User::find($id);
        $last_email = $user->email;
        $user_fields = ['name', 'surnames', 'user_type', 'color', 'email', 'password',];
        $email_val = 'required|email|unique:users';

        if (!$user->id){
            return $this->response()->error('resource_not_found', 500);
        }

        $userData = $request->only($user_fields);


        if( $last_email == $userData['email']){
            $email_val = 'required|email';
        }

        $validator = Validator::make($userData, [
            'name' => 'required|min:1',
            'surnames' => 'required|min:1',
            'user_type' => 'required|in:admin,masseuse',
            'color' => 'required|regex:/^#[a-f0-9]{6}$/i',
            'email' => $email_val,
            'password' => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $user->name = $userData['name'];
        $user->surnames = $userData['surnames'];
        $user->email = $userData['email'];
        $user->password = \Hash::make($userData['password']);
        $user->color = $userData['color'];
        $user->user_type = $userData['user_type'];

        $user->save();

        return $this->response->array($user)->setStatusCode(200);
    }

    public function destroy($id){
        $result = User::destroy($id);

        if(!$result){
            return $this->response->error('resource_not_found', 404);
        }

        return $this->response->array($result)->setStatusCode(200);
    }

}
