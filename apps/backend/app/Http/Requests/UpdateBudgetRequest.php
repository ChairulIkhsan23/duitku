<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Budget;

class UpdateBudgetRequest extends FormRequest
{
    /**
     * Validasi apakah user berhak mengupdate budget ini
     */
    public function authorize(): bool
    {
        $budget = $this->route('budget');

        // Hanya pemilik budget yang boleh update
        return $budget && $this->user()->id === $budget->user_id;
    }

    /**
     * Aturan validasi untuk update budget
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|uuid|exists:categories,id', // ID kategori valid
            'month_year' => 'required|date_format:Y-m', // Format bulan (YYYY-MM)
            'limit_amount' => 'required|numeric|min:0', // Limit budget harus angka positif
        ];
    }
}