<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
                'required',
                'string',
                'max:1000',
            ],
            'date' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],
            'status' => [
                'required',
                'string',
                Rule::in(TaskStatus::getValues()),
            ],
            'priority' => [
                'required',
                'string',
                Rule::in(TaskPriority::getValues()),
            ],
        ];
    }
}
