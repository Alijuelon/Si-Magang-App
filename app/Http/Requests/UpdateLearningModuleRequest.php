<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateLearningModuleRequest extends FormRequest
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
        $supervisor = Auth::user()->supervisor;

        return [
            'title' => 'sometimes|required|string|max:200',
            'description' => 'nullable|string',
            'intern_ids' => [
                'nullable',
                'array',
                Rule::in($supervisor->interns->pluck('id')) // Pastikan intern milik supervisor ini
            ],
            'intern_ids.*' => 'uuid|exists:interns,id',
        ];
    }
}
