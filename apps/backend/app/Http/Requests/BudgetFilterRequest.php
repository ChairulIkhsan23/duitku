<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetFilterRequest extends FormRequest
{
    /**
     * Aturan validasi untuk filter budget
     */
    public function rules()
    {
        return [
            'month_year' => ['nullable', 'date_format:Y-m'], // Format bulan (YYYY-MM)
        ];
    }

    /**
     * Custom message untuk validasi
     */
    public function messages()
    {
        return [
            'month_year.regex' => 'Month year must be in YYYY-MM format.',
        ];
    }
}