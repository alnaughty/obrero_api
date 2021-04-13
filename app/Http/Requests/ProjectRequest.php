<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages(){
        return [
            'name.required' => 'Un nom est requis',
            'name.unique' => 'Le nom doit être unique',
            'coordinates.required' => 'Les coordonnées sont obligatoires'
        ];
    }
    public function rules()
    {
        return [
            'name' => 'required|unique:projects|max:250',
            'coordinates' => 'required',
        ];
    }
}
