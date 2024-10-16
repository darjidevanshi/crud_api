<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use App\Models\User;
class UserController extends Controller
{
    //
    public function UpdateProfile(Request $request){
      
        if (!$request->user()) {
            return $this->prepareResult(false, [], 'Unauthorized.', Response::HTTP_UNAUTHORIZED);
        }
      
        try{
            
            $user = Auth::user();
           
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$user->id,
                'mobile' => 'required|numeric|digits_between:10,13',
                'dob' => 'date|nullable',
                'gender' => 'string|in:Male,Female,Other', 
                'address' => 'nullable|string|max:255',
            ], [
                'name.required' => 'The name field is required.',
                'email.required' => 'The email field is required.',
                'email.email' => 'The email must be a valid email address.',
                'email.unique' => 'The email has already been taken.',
                'mobile.required' => 'The mobile field is required.',
                'mobile.numeric' => 'The mobile must be numeric.',
                'mobile.digits_between' => 'The mobile must be between 10 and 13 digits.',
                'dob.date' => 'The date of birth must be a valid date.',
                'gender.string' => 'The gender must be a valid string.',
                'gender.in' => 'The gender must be either male, female, or other.',
                'address.string' => 'The address must be a valid string.',
            ]);
        
            
            $user->update($request->all());
        
            return $this->prepareResult(true, $user, 'Profile updated succesfully.', Response::HTTP_OK);

        }catch (ValidationException $e) {

            return $this->prepareResult(false, [], $e->validator->errors(), Response::HTTP_NON_AUTHORITATIVE_INFORMATION);

        }catch(Exception $e){

            return $this->prepareResult(false, [], $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        }

    }

    private function prepareResult($status, $data, $msg, $response_status_code){
       
        return response()->json(['account_active' => true, 'status' => $status, 'data' => $data, 'message' => $msg], $response_status_code);
    
    }
}
