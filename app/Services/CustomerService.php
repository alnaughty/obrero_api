<?php
namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Services\ImageUploader;

class CustomerService {
	public function __construct()
	{
		$this->uploader = new ImageUploader;
	}
	public function validate(Request $request){
		$validate = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|unique:customers|email',
			"address" => 'required',
			"contact_number" => 'required'
		]);
		if($validate->fails()){
			$result['status'] = false;
			$result['errors'] = $validate->errors();
			return $result;
		}
		$result['status'] = true;
		$result['data'] = $validate->validate();
		return $result;
	}
	public function validatePayment($requests)
	{
		$validate = Validator::make($requests, [
			'amount' => 'required',
			'customer_id' => 'required'
		]);
		if($validate->fails()){
			$result['status'] = false;
			$result['errors'] = $validate->errors();
			return $result;
		}
		$result['status'] = true;
		$result['data'] = $validate->validate();
		return $result;
	}
	public function updater($obj, $request)
	{
		if($request->first_name != null)
		{
			$obj->first_name = $request->first_name;
		}
		if($request->last_name != null)
		{
			$obj->last_name = $request->last_name;
		}
		if($request->address != null)
		{
			$obj->address = $request->address;
		}
		if($request->contact_number!= null)
		{
			$obj->contact_number = $request->contact_number;
		}
		if($request->picture != null)
		{
			$obj->picture = $this->uploader->getStorageUrl($request->picture,$obj->id,'customer');
		}
		$obj->save();
		return $obj;
	}

	public function paymentUpdater($obj, $request)
	{
		if($request->status != null)
		{
			$obj->status = $request->status;
		}
		if($request->amount != null)
		{
			$obj->amount = $request->amount;
		}
		$obj->save();
		return $obj;
	}

}