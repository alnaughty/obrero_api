<?php
namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Services\ImageUploader;
use App\UserStatus;
use App\FcmToken;
class UserService {
    public function __construct(){
        $this->uploader = new ImageUploader;
        $this->fcm = new FcmToken;
    }
	public function validate(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users|email',
            'password'=>'required',
            'contact_number'=>'required',
            'address'=>'required',
		]);
		if ($validator->fails()) { 
			$result['status'] = false;
			$result['error'] = $validator->errors();
	        return $result;        
        }else{

        	$result['data'] = $validator->validate();
        	$result['status'] = true;
        	$result['data']['password'] = bcrypt($result['data']['password']);
        	return $result;
        }
	}

    public function update($obj, $request)
    {
        if($request->first_name != null)
        {
            $obj->first_name = $request->first_name;
        }
        if($request->last_name != null)
        {
            $obj->last_name = $request->last_name;
        }
        if($request->contact_number != null)
        {
            $obj->contact_number = $request->contact_number;
        }
        if($request->address != null)
        {
            $obj->address = $obj->address;
        }
        if($request->picture != null)
        {
            $obj->picture = $this->uploader->getStorageUrl($request->picture,$obj->id,'user');
        }
        $obj->save();
        return $obj;
    }
    public function addFcmToken($user_id, $token)
    {
        $check = $this->fcm::where('token',$token)->where('user_id', $user_id)->first();
        if($check == null){
            $created = $this->fcm::create(
                [
                    "user_id" => $user_id,
                    'token' => $token,
                ]);
        }
    }
    public function changePassword($obj, $newPassword)
    {
        $obj->password = $newPassword;
        $obj->save();
        return true;
    }
    public function archive(){
        UserStatus::query()->each(function ($data){
            $ndata = $data->replicate();
            $ndata->setTable('user_status_archives');
            $ndata->save();

            $data->delete();
        });
    }
}