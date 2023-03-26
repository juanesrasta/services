<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    
	public function __construct(){
		$this->middleware('auth:api', ['except' => ['login','register']]);
	}	 
    
    public function login(Request $request){
        
		$request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'status_user_id' => 'required',
        ]);
		
        $credentials = $request->only('username', 'password', 'status_user_id');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
			'token' => $token,
            'authorization' => [
                'token' => $token,
                'type' => 'Bearer',
            ]
        ]);

    }
	
	
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:40'],
            'last_name' => ['required', 'string', 'max:40'],
            'username' => ['required', 'string', 'max:30', 'unique:users'],
            'password' => ['required', 'string', 'min:6','max:12'],
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
	
		$status_user_id = 1;
		if(isset($request->status_user_id) && !empty($request->status_user_id)){
			$status_user_id = $request->status_user_id;
		}
		
		$rol = 2;
		if(isset($request->rol) && !empty($request->rol)){
			$rol = $request->rol;
		}
		
		$user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'password' => bcrypt($request->password),
			'rol' => $rol,
			'status_user_id' => $status_user_id,
        ]);
	
		$token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
        
    }

    public function logout(){
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh(){
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
