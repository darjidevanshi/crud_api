<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Mail\SignOTPMail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
class AuthController extends Controller
{
    //
    public function Signup(Request $request){
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed'
            ], [
                'name.required' => 'The name field is required.',
                'email.required' => 'The email field is required.',
                'email.email' => 'The email must be a valid email address.',
                'email.unique' => 'The email has already been taken.',
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least 8 characters.',
                'password.confirmed' => 'The password do not match.'
            ]);

            $otp = rand(100000, 999999);

            Session::put('user_data', [
                'name' => $request->name,
                'email' => $request->email,
                'password' => ($request->password),
                'otp' => $otp,
            ]);
            Mail::to($request->email)->send(new SignOTPMail($request->email, $otp, $request->name));

            
            return $this->prepareResult(true, [], 'Signup Successful, OTP sent your email id. Please verify your OTP.', Response::HTTP_OK);


        } catch (ValidationException $e) {

            return $this->prepareResult(false, [], $e->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {

            return $this->prepareResult(false, [], $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    public function verifyOTP(Request $request){

        try{
            $request->validate(['otp' => 'required']);

            $data = Session::get('user_data');
            if (!$data || $request->otp != $data['otp']) {
    
                return $this->prepareResult(false, [], 'Invalid or expired OTP.', 400);

            }
    
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);
            
            Session::forget('user_data');

            return $this->prepareResult(true, [], 'User registered successfully.', 201);


        }catch (\Exception $e) {

            return $this->prepareResult(false, [], $e->getMessage(), 500);

        }
            

    }
    public function login(Request $request){

        try
        {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ],[
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'password.required' => 'The password field is required.',
            ]);
    
            if(!Auth::attempt($request->only('email', 'password'))){
                return $this->prepareResult(false, [], 'Invalid credentials. Please check your email and password and try again.', Response::HTTP_NON_AUTHORITATIVE_INFORMATION);

            }
    
            $user = Auth::user();

            $token = $user->createToken('UserToken')->plainTextToken;

            $user['token'] = $token;
          
            return $this->prepareResult(true, $user, '', Response::HTTP_OK);
   

        }catch (ValidationException $e) {

            return $this->prepareResult(false, [], $e->validator->errors(), Response::HTTP_NON_AUTHORITATIVE_INFORMATION);

        } catch (\Exception $e) {

            return $this->prepareResult(false, [], $e->getMessage(), Response::HTTP_NON_AUTHORITATIVE_INFORMATION);

        }
        
    }


    public function forgotPassword(Request $request){

        try{
            $request->validate(['email' => 'required|email']);

            $response = Password::sendResetLink($request->only('email'));

            return $response == Password::RESET_LINK_SENT
            ? response()->json(['message' => trans($response)], 200)
            : response()->json(['message' => trans($response)], 400);
        
        }catch (ValidationException $e) {

            return $this->prepareResult(false, [], $e->validator->errors(), Response::HTTP_NON_AUTHORITATIVE_INFORMATION);

        }catch(\Exception $e){

            return $this->prepareResult(false, [], $e->getMessage(), Response::HTTP_NON_AUTHORITATIVE_INFORMATION);

        }
    }

    public function resetPassword(Request $request){
      
        try{
      
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
                'token' => 'required|string',
            ]);
        
            $response = Password::reset($request->only('email', 'password', 'token'), function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            });
        
            return $response == Password::PASSWORD_RESET
                ? response()->json(['message' => 'Password has been reset.'], 200)
                : response()->json(['message' => 'Failed to reset password.'], 400);

        }catch (ValidationException $e) {

            return $this->prepareResult(false, [], $e->validator->errors(), Response::HTTP_NON_AUTHORITATIVE_INFORMATION);

        } catch (\Exception $e) {

            return $this->prepareResult(false, [], $e->getMessage(), Response::HTTP_NON_AUTHORITATIVE_INFORMATION);

        }
        
        
    }

    public function signout(Request $request)
    {
        if ($request->user()) {
            
            $request->user()->tokens()->delete();

            return $this->prepareResult(false, [], 'Successfully logged out.', Response::HTTP_OK);
        
        }
    
        return $this->prepareResult(false, [], 'No user is logged in.', Response::HTTP_UNAUTHORIZED);
    
    }
        private function prepareResult($status, $data, $msg, $response_status_code){
        
            return response()->json(['account_active' => true, 'status' => $status, 'data' => $data, 'message' => $msg], $response_status_code);
        
        }
}
