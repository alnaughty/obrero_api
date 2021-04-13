<?php
namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class ProjectService {
	public function createChecker(Request $request) 
	{
		$validator = Validator::make($request->all(), [
			'name' => 'required|unique:projects|max:250',
            'coordinates' => 'required',
            'customer_id' => 'required',
            'description'=>'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
		]);
		if ($validator->fails()) {
			$result['has_error'] = true;
			$result['errors'] = $validator->errors();
            return $result;
        }
        $result['has_error'] = false;
        $result['data'] = $validator->validate();
        $result['data']['start_date'] = $this->setDateAttribute($result['data']['start_date']);
        $result['data']['end_date'] = $this->setDateAttribute($result['data']['end_date']);
		return $result;
	}
	public function setDateAttribute($value)
	{
		return Carbon::parse($value);
	}
	function checkIsAValidDate($str){
    	return (bool)strtotime($str);
	}
	
}