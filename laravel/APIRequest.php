
<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UserRegisterRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed'
        ];
    }


    //isto serve para validações extra com erros custom
    //    protected function getValidatorInstance()
    //    {
    //        return parent::getValidatorInstance()->after(function ($validator) {
    //            $current_user = Auth::user();
    //            $attributes  = $this->only([ 'user_to','title','description']);
    //            // if($attributes['user_from'] == $attributes['user_to']){
    //            //   return $validator->errors()->add('user_from, user_to', 'You cant feedback yourself (lol)');
    //            // }
    //
    //
    //        });
    //    }

    //isto serve para passares a enviar os erros em json em vez de redirects
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
