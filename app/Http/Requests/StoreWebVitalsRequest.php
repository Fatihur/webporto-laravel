<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWebVitalsRequest extends FormRequest
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
            'path' => ['required', 'string', 'max:255'],
            'metric' => ['required', 'string', 'in:LCP,INP,CLS'],
            'value' => ['required', 'numeric', 'min:0'],
            'rating' => ['nullable', 'string', 'in:good,needs-improvement,poor'],
            'device_type' => ['nullable', 'string', 'max:20'],
            'connection_type' => ['nullable', 'string', 'max:30'],
            'recorded_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'metric.in' => 'Metric harus salah satu dari LCP, INP, atau CLS.',
            'rating.in' => 'Rating harus good, needs-improvement, atau poor.',
        ];
    }
}
