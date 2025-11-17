<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
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
            'description' => [
                'sometimes',
                'string',
                'max:1000',
            ],
            'date' => [
                'sometimes',
                'date',
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::in(TaskStatus::getValues()),
            ],
            'priority' => [
                'sometimes',
                'string',
                Rule::in(TaskPriority::getValues()),
            ],
        ];
    }
}
