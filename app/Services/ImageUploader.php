<?php
namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; 
use Image;
use Carbon\Carbon;

class ImageUploader {
	public function getStorageUrl($base64Image, $id, $storeAt)
	{
		$mytime = Carbon::now()->toDateTimeString();
		$extension = explode('/', explode(':', substr($base64Image, 0, strpos($base64Image, ';')))[1])[1];
		$replace = substr($base64Image, 0, strpos($base64Image, ',') + 1);
		$imagef = str_replace($replace, '', $base64Image); 
		$imagef = str_replace(' ', '+', $imagef);
		$imageName = $mytime.'-'.$id.'.'.$extension;
		Storage::disk($storeAt)->put($imageName, base64_decode($imagef));
		return Storage::url($storeAt.'/'.$imageName);
	}
}