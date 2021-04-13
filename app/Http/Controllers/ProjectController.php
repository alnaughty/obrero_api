<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\ProjectWarning;
use App\Services\ProjectService;
use App\Services\GeneralService;
use App\Services\ImageUploader;
// use App\Http\Requests\ProjectRequest;
class ProjectController extends Controller
{
	public function __construct()
	{
		$this->project = new Project;
		$this->warning = new ProjectWarning;
		$this->service = new ProjectService;
		$this->uploader = new ImageUploader;
		$this->gen_service = new GeneralService;
	}
	public function index($page){
		$data = $this->project::with('owner.status', 'warnings')->paginate($page);
		return response()->json($data);
	}
    public function create(Request $request)
    {
    	$validated = $this->service->createChecker($request);
    	if($validated['has_error']){	
    		return response()->json($validated,422);
    	}
    	$created = $this->project::create($validated['data']);
    	return response()->json(['message' => "Created Successfully", 'data' => $created]);
    }
    public function remove($id)
    {
    	$delete = $this->project::find($id)->delete();
    	return response()->json(['message' => "Deleted Successfully"],200);
    	
    }
    public function update(Request $request)
    {
    	$toUpdate = Auth::user();
    	if($toUpdate != null){
    		if($request->name != null)
	    	{
	    		$toUpdate->name = $request->name;
	    	}
	    	if($request->coordinates != null)
	    	{
	    		$toUpdate->coordinates = $request->coordinates;
	    	}
	    	if($request->start_date != null)
	    	{
	    		if($this->service->checkIsAValidDate($request->start_date)){
	    			$toUpdate->start_date = $this->service->setDateAttribute($request->start_date);
	    		}else{
	    			return response()->json(['message' => "Invalid date format"],422);
	    		}
	    	}
	    	if($request->end_date != null)
	    	{
	    		if($this->service->checkIsAValidDate($request->end_date)) {
	    			$toUpdate->end_date = $this->service->setDateAttribute($request->end_date);
	    		}else{
	    			return response()->json(['message' => "Invalid date format"],422);
	    		}
	    		
	    	}
	    	if($request->description != null)
	    	{
	    		$toUpdate->description = $request->description;
	    	}
	    	$toUpdate->save();
	    	return response()->json(['message' => "Update Successfully", 'data' =>$toUpdate],200);
    	}else{
    		return response()->json(['message' => 'Project not found, nothing to update'], 404);
    	}
    }

    //Warning
    public function addWarning(Request $request)
    {
    	$inputs = $request->all();
    	if($request->picture != null){
    		$inputs['picture'] = $this->uploader->getStorageUrl($request->picture, $request->project_id, 'project_warning');
    	}
    	$created = $this->warning::create($inputs);
    	if($created != null)
    	{
    		return response()->json(['message' => "created successfully"],200);
    	}
    	return response()->json(['message' => "creation failed"],422);
    }
    public function removeWarning($id){
    	$to = $this->warning::find($id);
    	return $this->gen_service->delete($to);
    }
}
