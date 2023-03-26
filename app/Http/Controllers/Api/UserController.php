<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Models\User;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api');
    }
	
    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => 'success',
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'txtfname' => ['required', 'string'],
			'txtlname' => ['required', 'string'],
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
    }

    public function show(string $id)
    {
        $user = User::find($id);
        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }

    public function update(Request $request, string $id)
    {	
		if(isset($request->uniq_name) && !empty($request->uniq_name) && $request->uniq_name == $request->username){
			$validator = Validator::make($request->all(), [
				'first_name' => ['required', 'string', 'max:40'],
				'last_name' => ['required', 'string', 'max:40'],
			]);
		}else{
			$validator = Validator::make($request->all(), [
				'first_name' => ['required', 'string', 'max:40'],
				'last_name' => ['required', 'string', 'max:40'],
				'username' => ['required', 'string', 'max:30', 'unique:users'],
			]);
		}
			
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::find($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $request->username;
		if(isset($request->password) && !empty($request->password)){
			$user->password = bcrypt($request->password);
		}
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }
	
	public function userstt(Request $request){

		$validator = Validator::make($request->all(), [
			'stt' => ['required'],
			'id' => ['required'],
		]);
		
		if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
		
		$user = User::find($request->id);
        $user->status_user_id = $request->stt;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
	}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
		
        $user = User::find($id);
        $user->delete();
		
		//Por tratarse de una prueba realizare eliminacion fisica
		//en proyectos formales se definen las eliminaciones, pudiendo set fisicas o logicas
        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
            'category' => $user,
        ]);
    }
}
