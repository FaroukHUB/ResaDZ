<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public booking form
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'pickup_time' => [
                'required',
                'string',
                'max:5',
                'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'
            ],
            'client_name' => 'required|string|min:2|max:255',
            'client_phone' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^[\d\s\+\-\(\)]+$/'
            ],
            'client_email' => 'required|email:rfc,dns|max:255',
            'pickup_zone_id' => 'nullable|exists:delivery_zones,id',
            'return_zone_id' => 'nullable|exists:delivery_zones,id',
            'same_return_location' => 'nullable|in:0,1',
            'options' => 'nullable|array',
            'options.*' => 'string|max:100',
            'currency' => 'nullable|in:DZD,EUR',
            'advance_payment_method' => 'nullable|in:cash,cib,dahabia,baridimob,paypal,bank_transfer',
            'internal_notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Veuillez sélectionner un véhicule.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'start_date.required' => 'La date de début est requise.',
            'start_date.date' => 'La date de début n\'est pas valide.',
            'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou une date future.',
            'end_date.required' => 'La date de fin est requise.',
            'end_date.date' => 'La date de fin n\'est pas valide.',
            'end_date.after' => 'La date de fin doit être après la date de début.',
            'pickup_time.required' => 'L\'heure de prise en charge est requise.',
            'pickup_time.regex' => 'Le format de l\'heure doit être HH:MM (ex: 09:00).',
            'client_name.required' => 'Votre nom est requis.',
            'client_name.min' => 'Le nom doit contenir au moins 2 caractères.',
            'client_name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'client_phone.required' => 'Le numéro de téléphone est requis.',
            'client_phone.min' => 'Le numéro de téléphone doit contenir au moins 8 chiffres.',
            'client_phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'client_phone.regex' => 'Le numéro de téléphone contient des caractères invalides.',
            'client_email.required' => 'L\'adresse email est requise.',
            'client_email.email' => 'L\'adresse email n\'est pas valide.',
            'pickup_zone_id.exists' => 'La zone de prise en charge sélectionnée n\'existe pas.',
            'return_zone_id.exists' => 'La zone de retour sélectionnée n\'existe pas.',
            'currency.in' => 'La devise doit être DZD ou EUR.',
            'advance_payment_method.in' => 'La méthode de paiement sélectionnée n\'est pas valide.',
            'internal_notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize phone number: remove extra spaces
        if ($this->has('client_phone')) {
            $this->merge([
                'client_phone' => preg_replace('/\s+/', ' ', trim($this->client_phone)),
            ]);
        }

        // Normalize email: lowercase and trim
        if ($this->has('client_email')) {
            $this->merge([
                'client_email' => strtolower(trim($this->client_email)),
            ]);
        }

        // Normalize client name: trim
        if ($this->has('client_name')) {
            $this->merge([
                'client_name' => trim($this->client_name),
            ]);
        }

        // Convert empty string to null for optional fields
        if ($this->advance_payment_method === '') {
            $this->merge(['advance_payment_method' => null]);
        }
    }
}
