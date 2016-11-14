<?php

namespace App\Api\V1\Controllers;
use App\Http\Requests;

use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use JWTAuth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Validator;
use App\User;
use App\Phone;


class PhoneController extends Controller
{
    //
    use Helpers;

    public function index(){
        return Phone::all();
    }

    public function getUserPhones($user_id){
        $user = User::find($user_id);

        if(!$user->id){
            return $this->response()->error('resource_not_exists', 500);
        }

        return $user->phones;
    }

    public function showPhone($phone_id){
        $phone = Phone::find($phone_id);

        if(! $phone->id){
            return $this->response()->error('resource_not_found', 500);
        }

        return $phone;
    }

    public function store(Request $request, $user_id){
        $user = User::find($user_id);

        if(!$user->id){
            return $this->response()->error('resource_not_exists', 500);
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

            return $this->response()->created();
        }


    }


    public function update(Request $request, $phone_id){
        $phone = Phone::find($phone_id);
        $last_number = $phone->number;
        $validation_number = ['regex:/^[0-9]{11}$/','required','unique:phones'];

        if(! $phone->id){
            return $this->response()->error('resource_not_found', 500);
        }

        if($request->has('number') && $request->has('description')){

            $number_provided = $request->number;
            $description_provided = $request->description;

            if($number_provided == $last_number){
                $validation_number = ['regex:/^[0-9]{11}$/','required'];
            }

            $validator_phone = Validator::make([$number_provided, $description_provided], ['number' => $validation_number, 'description' => 'required|min:5']);

            if ($validator_phone->fails()){
                throw new ValidationHttpException($validator_phone->errors()->all());
            } else {
                $phone->number = $number_provided;
                $phone->desription = $description_provided;
                $phone->save();

                return $this->response->array($phone)->setStatusCode(200);
            }

        } else {
            return $this->response()->error('emails_not_provided', 500);
        }
    }

    public function destroy($phone_id){
        $phone = Phone::find($phone_id);

        if(! $phone->id){
            return $this->response()->error('resource_not_exists', 500);
        }

        $result = Phone::destroy($phone_id);

        if(! $result){
            return $this->response()->error('error_wrong_something_happens_on_delete', 500);
        }

        return $this->response->array($phone_id)->setStatusCode(200);

    }


}
