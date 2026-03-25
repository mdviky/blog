<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //return false;
        return auth()->check() && auth()->user()->role === 'admin';

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */    

    // Validation rules
    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'status'      => 'required|in:draft,published',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
        ];
    }

    // Custom error messages (optional)
    public function messages(): array
    {
        return [
            'title.required'  => 'Please enter a post title.',
            'body.required'   => 'Please enter the post content.',
            'status.required' => 'Please select a status.',
            'status.in'       => 'Status must be draft or published.',
        ];
    }
}
