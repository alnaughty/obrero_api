<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;
use App\UserStatus;
use Carbon\Carbon;
use App\Services\GeneralService;
class UserController extends Controller
{
	public function __construct()
	{
		$this->user = new User;
		$this->service = new UserService;
		$this->gen_service = new GeneralService;
		$this->status = new UserStatus;
	}
	public function getUser($id) {
		return $this->user::find($id)->load('status','archived_status');
	}
	public function index($page)
	{
		$res = $this->user::with('status','archived_status')->paginate($page);
		return response()->json($res,200);
	}
	public function update(Request $request){
		$toUpdate = Auth::user();
		if($toUpdate != null){
			$updated = $this->service->update($toUpdate, $request);
			if($updated != null)
			{
				return response()->json(['message' => "Update successful"],200);
			}
			return response()->json(['message' => 'Unable to update profile'],422);
		}
		return response()->json(['message' => "User not found"],404);
	}
	public function remove($user_id){
		$toDelete = $this->user::find($user_id);
    	return $this->gen_service->delete($toDelete);
	}
	public function changePassword(Request $request)
	{
		$toChange = $this->user::find($request->id);
		if($toChange != null) {
			if($this->service->changePassword($toChange, $request->new_password)){
				return response()->json(['message' => "Password changed"],200);
			}
			return response()->json(['message' => "Unable to change password"],422);
		}
		return response()->json(['message' => "User not found"],404);
	}
	public function createStatus($user_id,$isIn){
		$created = $this->status::create([
			"user_id" => $user_id,
			"status" => $isIn,
		]);
		return $created;
	}
	public function timeUpdate($isIn){
		$now = Carbon::now()->toDateTimeString();
		$user = Auth::user();
		$obj = $this->status::where('user_id', $user->id)->first();
		if($obj != null){
			if($isIn == 1){
				$obj->time_in = $now;
			}else{
				$obj->time_out = $now;
			}
			$obj->status = intval($isIn);
			$obj->save();
			return response()->json(['message' => 'Update successful', 'data' => $obj],200);
		}
		$created = $this->createStatus($user->id,1);
		return response()->json(['message' => 'Creation successful', 'data' => $created],200);
	}

	public function notificationControl($isOn)
	{
		$user = Auth::user();
		$data;
		if($isOn){
			$data = $this->turnOnNotification($user);
		}else{
			$data = $this->turnOffNotification($user);
		}
		return response()->json($data,200);
	}
	public function turnOffNotification($user){
		$user->enable_notification = 0;
		$user->save();
		return $user;
	}
	public function turnOnNotification($user){
		$user->enable_notification = 1;
		$user->save();
		return $user;
	}
}
