<?php

namespace App\Api\V1\Controllers;

use App\User;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Email;
use App\Http\Requests;

class EmailController extends Controller
{
    use Helpers;

    public function index(){
        return Email::all();
    }

    public function getUserEmails($user_id){
        $user = User::find($user_id);

        if(!$user->id){
            return $this->response()->error('resource_not_exists', 500);
        }

        return $user->emails;
    }

    public function showEmail($email_id){

        $email = Email::find($email_id);

        if(! $email->id){
            return $this->response()->error('resource_not_found',500);
        }

        return $email;


    }

    public function store(Request $request, $user_id){

        $user = User::find($user_id);

        if (! $user->id ){
            return $this->response()->error('resource_not_exists', 500);
        }

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

            return $this->response()->created();

        } else {
            return $this->response()->error('emails_not_provided', 500);
        }

    }

    public function update(Request $request, $email_id){
        $email = Email::find($email_id);
        $last_email = $email->email;
        $validation_regex = 'required|email|unique:users|unique:emails';

        if(! $email->id){
            return $this->response()->error('resource_not_found', 500);
        }

        if($request->has('email')){

            $email_provided = $request->email;

            if($email_provided == $last_email){
                $validation_regex = 'required|email';
            }

            $validator_email = Validator::make($email_provided, ['email' => $validation_regex]);

            if ($validator_email->fails()){
                throw new ValidationHttpException($validator_email->errors()->all());
            } else {
                $email->email = $email_provided;
                $email->save();

                return $this->response->array($email)->setStatusCode(200);
            }

        } else {
            return $this->response()->error('array_emails_not_provided', 500);
        }

    }

    public function destroy($email_id){
        $email = Email::find($email_id);

        if(! $email->id){
            return $this->response()->error('resource_not_found', 500);
        }

        $result = Email::destroy($email_id);

        if(!$result){
            return $this->response->error('resource_not_found', 404);
        }

        return $this->response->array($result)->setStatusCode(200);
    }

}
