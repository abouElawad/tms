<?php

namespace App\Http\Requests;

use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRoleRequest extends FormRequest
{
  use ApiResponseTrait;
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'name' => 'required|string|min:3|unique:roles,name'
    ];
  }

  public function failedValidation(Validator $validator)
  {
     throw new HttpResponseException(
        $this->apiResponse(422,'validation error',$validator->errors())
    );
  }
}
