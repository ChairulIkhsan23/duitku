<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    /**
     * Izinkan semua user untuk membuat budget
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Aturan validasi untuk membuat budget baru
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|uuid|exists:categories,id', // ID kategori harus valid
            'month_year' => 'required|date_format:Y-m', // Format bulan (YYYY-MM)
            'limit_amount' => 'required|numeric|min:0', // Limit budget harus angka positif
        ];
    }
}