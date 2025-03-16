<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(Request $request){
        $validator= Validator::make($request->all(), [
            'name'=>'required|max:191',
            'email'=>'required|email|max:191|unique:users,email',
            'password'=>'required|min:8',
            'password_confirmation'=>'required|min:8',
        ]);
        if ($validator->fails()){

            return response()->json([
'validation_errors'=>$validator->messages()
            ]);
        }else{
            $user= User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
                'password_confirmation'=>Hash::make($request->password_confirmation),
            ]);

         $token = $user->createToken($user->email.'_Token')->plainTextToken;

         return response()->json([
    'status'=>200,
    'username'=>$user->name,
    'token'=>$token,
    'message'=>'Registered Successfully.',
            ]);
        }
    }

    public function login(Request $request){

        $validator= Validator::make($request->all(), [
            'email'=>'required|email|max:191',
            'password'=>'required',
            
        ]);
        if ($validator->fails()){

            return response()->json([
'validation_errors'=>$validator->messages()
            ]);
        }else{
            $user = User::where('email', $request->email)->first();
 
    if (! $user || ! Hash::check($request->password, $user->password)) {
       return response()->json(
        [
            'status'=>401,
            'message'=>'Invalid Credentials.',
        ] );
       }else{
            if ($user->status == 0) {  // Assuming 0 means blocked
                            return response()->json([
                                'status' => 403,
            'message' => 'Your account is blocked. Please contact the administrator if you feel this is an error.',
            'username' => $user->name, // Include username for consistency
            'role' => $user->role_as == 1 ? 'admin' : '', // Include role for consistency
                            ]);
                        }
        if($user->role_as==1)//1 =admin 
        {
            $role= 'admin';
        $token = $user->createToken($user->email.'_AdminToken', ['server:admin'])->plainTextToken;
        }else{
             $role= '';
        $token = $user->createToken($user->email.'_Token', [''])->plainTextToken;
        }

         return response()->json([
        'status'=>200,
        'username'=>$user->name,
        'token'=>$token,
        'message'=>'Logged In Successfully.',
        'role'=>$role,
                ]);
            }
            }

       }


    public function logout(){
    auth()->user()->tokens()->delete();
        return response()->json([
    'status'=>200,
    'message'=>'Logged out Successfully.',
            ]);
        }

    }
 
