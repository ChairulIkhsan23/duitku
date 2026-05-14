<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\CategoryType;
use Illuminate\Validation\Rules\Enum;

class CategoryRequest extends FormRequest
{
    /**
     * Izinkan semua user yang sudah melewati middleware auth
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk request category
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50', // Nama kategori
            'type' => ['required', new Enum(CategoryType::class)], // income / expense
            'icon' => 'nullable|string|max:50', // Icon kategori (opsional)
            'color' => 'nullable|string|max:7', // Warna hex (opsional)
            'budget_default' => 'nullable|numeric|min:0', // Default budget kategori
        ];
    }
}