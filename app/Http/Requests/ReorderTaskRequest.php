<?php

namespace App\Http\Requests;

use App\Models\BoardList;
use Illuminate\Foundation\Http\FormRequest;

class ReorderTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $list = BoardList::findOrFail($this->input('list_id'));
        return $this->user()->can('update', $list->board);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'task_id' => 'required|exists:tasks,id',
            'list_id' => 'required|exists:lists,id',
            'tasks' => 'required|array',
            'tasks.*' => 'numeric|exists:tasks,id'
        ];
    }
}
