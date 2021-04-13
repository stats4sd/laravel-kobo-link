<?php


namespace Stats4sd\KoboLink\Http\Requests;


class XlsformRequest extends \Illuminate\Foundation\Http\FormRequest
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
            'xlsfile' => ['required'],
            'description' => ['nullable', 'max:60000'],
            'media' => ['nullable'],
            'csv_lookups' => ['nullable', 'json'],
            'is_active' => ['boolean'],
            'available' => ['boolean'],
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            //
        ];
    }
}
