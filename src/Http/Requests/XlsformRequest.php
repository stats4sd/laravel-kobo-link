<?php


namespace Stats4sd\KoboLink\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class XlsformRequest extends FormRequest
{
    /**
         * Determine if the user is authorized to make this request.
         *
         * @return bool
         */
    public function authorize(): bool
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'xlsfile' => ['sometimes', 'required'],
            'description' => ['nullable', 'max:60000'],
            'media' => ['nullable'],
            'csv_lookups' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'available' => ['boolean'],
        ];
    }
}
