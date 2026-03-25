<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'       => 'sometimes|string|max:255',
            'body'        => 'sometimes|string',
            'category_id' => 'nullable|exists:categories,id',
            'status'      => 'sometimes|in:draft,published',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [            
            'status.in'       => 'Status must be draft or published.',
        ];
    }
}
