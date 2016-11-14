<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

use JWTAuth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Validator;
use App\User;
use App\Address;

class AddressController extends Controller
{
    //
    use Helpers;

    public function index(){
        return Address::all();
    }

    public function getUserAdresses($user_id){
        $user = User::find($user_id);

        if(!$user->id){
            return $this->response()->error('resource_not_exists', 500);
        }

        return $user->addresses;
    }

    public function showAddress($address_id){
        $address = Address::find($address_id);

        if(! $address->id){
            return $this->response()->error('resource_not_found', 500);
        }

        return $address;
    }

    public function store(Request $request, $user_id){
        $user = User::find($user_id);

        if(!$user->id){
            return $this->response()->error('resource_not_exists', 500);
        }

        if($request->has('addresses')){

            $addresses = $request->phones;
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

            return $this->response()->created();
        }


    }


    public function update(Request $request, $address_id){
        $address = Address::find($address_id);
        $last_address = $address->address;
        $validation_address = 'required|min:5|unique:addresses';

        if(! $address->id){
            return $this->response()->error('resource_not_found', 500);
        }

        if($request->has('addresses') && $request->has('main')){

            $address_provided = $request->address;
            $main_provided = $request->main;

            if($address_provided == $last_address){
                $validation_address = 'required|min:5';
            }

            $validator_address = Validator::make([$address_provided, $main_provided],['address' => $validation_address, 'main' => 'boolean']);

            if ($validator_address->fails()){
                throw new ValidationHttpException($validator_address->errors()->all());
            } else {
                $address->address = $address_provided;
                $address->desription = $main_provided;
                $address->save();

                return $this->response->array($address)->setStatusCode(200);
            }

        } else {
            return $this->response()->error('emails_not_provided', 500);
        }
    }

    public function destroy($address_id){
        $address = Address::find($address_id);

        if(! $address->id){
            return $this->response()->error('resource_not_exists', 500);
        }

        $result = Address::destroy($address_id);

        if(! $result){
            return $this->response()->error('error_wrong_something_happens_on_delete', 500);
        }

        return $this->response->array($address_id)->setStatusCode(200);

    }


}
