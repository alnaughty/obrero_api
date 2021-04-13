<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;
use App\FcmToken;
use App\Services\UserService;
use App\Services\GeneralService;
use App\Http\Controllers\UserController;

class AuthController extends Controller
{
	public function __construct()
	{
        $this->controller = new UserController;
		$this->user = new User;
		$this->service = new UserService;
        $this->token = new FcmToken;
        $this->gen_serve = new GeneralService;
	}
    public function login(Request $request)
    { 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')]))
        { 
            $user = Auth::user()->load('status');
            $data['token'] = $user->createToken('Obrero')->accessToken; 
            $data['details'] = $user;
            $this->service->addFcmToken($user->id, $request->fcm_token);
            return response()->json(['data' => $data], 200); 
        }
        else{ 
            return response()->json(['error'=>'Unauthorized'], 401); 
        } 
    }
    public function admin_login(Request $request)
    {
    	if(Auth::attempt(['email' => request('email'), 'password' => request('password')]))
        { 
            $user = Auth::user()->load('status','archived_status');
            if($user->is_admin == 1){
            	$data['token'] = $user->createToken('Obrero')->accessToken; 
	            $data['details'] = $user;
                $this->service->addFcmToken($user->id, $request->fcm_token);
	            return response()->json(['data' => $data], 200); 
            }else{
            	return response()->json(['message' => "Unprivilidged user"], 422);
            }
        }
        else{ 
            return response()->json(['error'=>'Unauthorized'], 401); 
        }
    }
    public function register(Request $request)
    {
    	$data = $this->service->validate($request);
    	if($data['status']){
    		$user = $this->user::create($data['data']);
            $this->controller->createStatus($user->id, 0);
            $user = $this->user::find($user->id)->load('status');
    		$result['details'] = $user;
    		$result['token'] = $user->createToken('Obrero')->accessToken;
            
    		return response()->json(['data' => $result], 200); 
    	}
    	return response()->json($data,422);
    }

    public function logout(Request $request)
    {
        $user_id = Auth::id();
        $validated = $this->gen_serve->manual_validator($request->all(), [
            "fcm_token" => 'required'
        ]);
        if($validated['status'])
        {
            $this->token::where('user_id', $user_id)->where('token', $validated['data']['fcm_token'])->delete();
            return response()->json(['message' => "Logout Sucessful"],200);
        }
        return response()->json($validated,500);
    }
}
