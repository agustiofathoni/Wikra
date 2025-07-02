<?php

namespace App\Http\Requests;

use App\Models\BoardList;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'list_id' => 'required|exists:lists,id'
        ];
    }
}
