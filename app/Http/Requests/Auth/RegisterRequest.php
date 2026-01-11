<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            // User fields
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],

            // Organization fields
            'organization_name' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'in:FR,DE'],
            'sector' => ['nullable', 'string', 'max:255'],
            'organization_size' => ['nullable', 'string', 'in:1-10,11-50,51-250,251-500,500+'],

            // Terms acceptance
            'accept_terms' => ['required', 'accepted'],
            'accept_privacy' => ['required', 'accepted'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.name'),
            'first_name' => __('validation.attributes.first_name'),
            'last_name' => __('validation.attributes.last_name'),
            'email' => __('validation.attributes.email'),
            'password' => __('validation.attributes.password'),
            'organization_name' => __('validation.attributes.organization_name'),
            'country' => __('validation.attributes.country'),
            'sector' => __('validation.attributes.sector'),
            'organization_size' => __('validation.attributes.organization_size'),
            'accept_terms' => __('validation.attributes.accept_terms'),
            'accept_privacy' => __('validation.attributes.accept_privacy'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => __('auth.email_taken'),
            'password.confirmed' => __('auth.password_confirmation_mismatch'),
            'country.in' => __('auth.invalid_country'),
            'accept_terms.accepted' => __('auth.terms_required'),
            'accept_privacy.accepted' => __('auth.privacy_required'),
        ];
    }
}
