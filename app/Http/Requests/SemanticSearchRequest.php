<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Semantic Search Request Validation
 *
 * Validates requests for the semantic search API endpoints.
 */
class SemanticSearchRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:2', 'max:500'],
            'index' => ['required', 'string', 'in:emission_factors,transactions,documents,actions'],
            'top_k' => ['nullable', 'integer', 'min:1', 'max:100'],
            'min_score' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'filters' => ['nullable', 'array'],
            'filters.scope' => ['nullable', 'integer', 'in:1,2,3'],
            'filters.source' => ['nullable', 'string', 'max:50'],
            'filters.country' => ['nullable', 'string', 'size:2'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'query.required' => 'La requête de recherche est obligatoire.',
            'query.min' => 'La requête doit contenir au moins 2 caractères.',
            'query.max' => 'La requête ne peut pas dépasser 500 caractères.',
            'index.required' => 'L\'index de recherche est obligatoire.',
            'index.in' => 'L\'index doit être: emission_factors, transactions, documents ou actions.',
            'top_k.min' => 'Le nombre de résultats doit être au moins 1.',
            'top_k.max' => 'Le nombre de résultats ne peut pas dépasser 100.',
            'min_score.min' => 'Le score minimum doit être entre 0 et 1.',
            'min_score.max' => 'Le score minimum doit être entre 0 et 1.',
        ];
    }

    /**
     * Get the validated data with defaults.
     */
    public function validatedWithDefaults(): array
    {
        $validated = $this->validated();

        return array_merge([
            'top_k' => 10,
            'min_score' => config('usearch.search.default_min_score', 0.5),
            'filters' => null,
        ], $validated);
    }
}
