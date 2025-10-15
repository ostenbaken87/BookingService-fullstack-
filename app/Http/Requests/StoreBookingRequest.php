<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreBookingRequest extends FormRequest
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
            'service_id' => 'required|integer|exists:services,id',
            'client_name' => 'required|string|max:255|min:2',
            'client_phone' => 'required|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'start_time' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Необходимо выбрать услугу',
            'service_id.exists' => 'Выбранная услуга не найдена',
            'client_name.required' => 'Необходимо указать имя',
            'client_name.min' => 'Имя должно содержать минимум 2 символа',
            'client_phone.required' => 'Необходимо указать номер телефона',
            'client_phone.regex' => 'Некорректный формат номера телефона',
            'start_time.required' => 'Необходимо указать время начала',
            'start_time.after' => 'Время начала должно быть в будущем',
            'notes.max' => 'Заметки не должны превышать 1000 символов',
        ];
    }
}
