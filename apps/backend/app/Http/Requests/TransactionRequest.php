<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TransactionType;
use Illuminate\Validation\Rules\Enum;

class TransactionRequest extends FormRequest
{
    /**
     * Izinkan semua user yang sudah authenticated
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk transaksi
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1', // Nominal transaksi
            'type' => ['required', new Enum(TransactionType::class)], // income / expense
            'category_id' => 'required|uuid|exists:categories,id', // ID kategori (validasi utama)
            'date' => 'nullable|date', // Tanggal transaksi (opsional)
            'note' => 'nullable|string|max:255', // Catatan transaksi
            'photo_url' => 'nullable|url', // URL bukti transaksi
            'location_name' => 'nullable|string|max:255', // Lokasi transaksi
        ];
    }

    /**
     * Custom message untuk validasi transaksi
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Nominal transaksi wajib diisi',
            'amount.min' => 'Nominal minimal Rp 1',
            'type.required' => 'Tipe transaksi wajib dipilih',
            'type.in' => 'Tipe transaksi harus income atau expense',
            'date.date' => 'Format tanggal tidak valid',
            'category_id.uuid' => 'Format category ID tidak valid',
            'category_id.exists' => 'Kategori tidak ditemukan',
        ];
    }
}