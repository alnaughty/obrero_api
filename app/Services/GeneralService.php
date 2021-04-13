<?php
namespace App\Services;
use Illuminate\Support\Facades\Validator;

class GeneralService {

	public function delete($obj)
	{
		if($obj != null){
			$obj->delete();
			return response()->json(['message' => "Delete successful"],200);
		}
		return response()->json(['message' => "Object not found"], 404);
	}

	public function create($model, $data)
	{
		$create = $model::create($data);
		return $create;
	}
	public function manual_validator($data, $validation_condition)
	{
		$validate = Validator::make($data, $validation_condition);
		if($validate->fails()){
			$result['status'] = false;
			$result['errors'] = $validate->errors();
			return $result;
		}
		$result['status'] = true;
		$result['data'] = $validate->validate();
		return $result;
	}
}