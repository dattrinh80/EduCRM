<?php

declare(strict_types=1);

namespace Modules\Marketing\Campaign\Presentation\Web\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Middleware handles this, but can be refined
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isGlobalScope = false;
        try {
            $isGlobalScope = app('is_global_scope');
        } catch (\Exception $e) {}

        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:campaigns,code',
            'channel' => 'nullable|string|max:100',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];

        if ($isGlobalScope) {
            $rules['center_id'] = 'required|uuid|exists:centers,id';
        }

        return $rules;
    }

    /**
     * Get the validated data including center resolution.
     */
    public function getValidatedData(): array
    {
        $validated = $this->validated();
        
        $isGlobalScope = false;
        try {
            $isGlobalScope = app('is_global_scope');
        } catch (\Exception $e) {}

        $validated['center_id'] = $isGlobalScope
            ? ($validated['center_id'] ?? null)
            : (session('current_center_id') ?? app('center_id'));

        return $validated;
    }
}
