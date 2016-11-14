<?php

namespace App\Api\V1\Controllers;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;


use JWTAuth;
use Validator;
use App\User;
use App\Social;
use App\Http\Requests;

class SocialController extends Controller
{
    use Helpers;

    public function index(){
        return Social::all();
    }

    public function getUserSocials($user_id){
        $user = User::find($user_id);

        if(!$user->id){
            return $this->response()->error('resource_not_exists', 500);
        }

        return $user->socials;
    }

    public function showSocial($social_id){
        $social = Social::find($social_id);

        if(!$social->id){
            return $this->response()->error('resource_not_found',500);
        }
        return $social;
    }

    public function store(Request $request, $user_id){
        $user = User::find($user_id);

        if(!$user->id){
            return $this->response()->error('resource_not_exists', 500);
        }

        if($request->has('socials')){

            $socials = $request->socials;
            $to_save =[];

            foreach ($socials as $social) {
                $social_values = ['uri' => $social['uri'], 'aplication' => $social['aplication']];

                $validator_social = Validator::make($social_values,  ['uri' => 'required|min:5|unique:socials', 'aplication' => 'required|min:5']);

                if ($validator_social->fails()){
                    throw new ValidationHttpException($validator_social->errors()->all());
                } else {
                    array_push($to_save, new Social($social_values));
                }
            }
            $user->socials()->saveMany($to_save);

            return $this->response()->created();
        } else {
            return $this->response()->error('aaray_socials_not_provided', 500);
        }


    }


    public function update(Request $request, $social_id){
        $social = Social::find($social_id);
        $last_uri = $social->uri;
        $validation_uri = 'required|min:5|unique:socials';

        if(! $social->id){
            return $this->response()->error('resource_not_found', 500);
        }

        if($request->has('uri') && $request->has('aplication')){

            $uri_provided = $request->uri;
            $aplication_provided = $request->aplication;

            if($uri_provided == $last_uri){
                $validation_uri = 'required|min:5';
            }

            $validator_social = Validator::make([$uri_provided, $aplication_provided], [$validation_uri, 'aplication' => 'required|min:5']);

            if ($validator_social->fails()){
                throw new ValidationHttpException($validator_social->errors()->all());
            } else {

                $social->uri = $uri_provided;
                $social->aplication = $aplication_provided;
                $social->save();

                return $this->response->array($social)->setStatusCode(200);
            }

        } else {
            return $this->response()->error('euri_or_aplication_not_provided', 500);
        }
    }

    public function destroy($social_id){
        $social = Social::find($social_id);

        if(! $social->id){
            return $this->response()->error('resource_not_exists', 500);
        }

        $result = Social::destroy($social_id);

        if(! $result){
            return $this->response()->error('error_wrong_something_happens_on_delete', 500);
        }

        return $this->response->array($social_id)->setStatusCode(200);

    }
}
