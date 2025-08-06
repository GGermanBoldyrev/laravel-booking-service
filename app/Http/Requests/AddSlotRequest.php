<?php

namespace App\Http\Requests;

use App\Rules\NoOverlap;
use Illuminate\Foundation\Http\FormRequest;

class AddSlotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('booking')->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:Y-m-d\TH:i:s', new NoOverlap()],
            'end_time' => ['required', 'date_format:Y-m-d\TH:i:s', 'after:start_time'],
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'You are not authorized to add a slot to this booking.'
        ];
    }
}
