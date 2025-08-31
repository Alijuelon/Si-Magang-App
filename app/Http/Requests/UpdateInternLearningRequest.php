<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateInternLearningRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:200',
            'description' => 'nullable|string',
            'progress_status' => ['sometimes','required', 'string', Rule::in(['in_progress', 'done'])],
            'module_id' => 'sometimes|required|uuid|exists:learning_modules,id',
        ];
    }
}
