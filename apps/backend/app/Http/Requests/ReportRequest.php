<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    /**
     * Izinkan semua user untuk generate report
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk request report
     */
    public function rules(): array
    {
        return [
            'start_date' => 'required|date', // Tanggal mulai report
            'end_date' => 'required|date|after_or_equal:start_date', // Tanggal akhir harus >= start_date
            'format' => 'nullable|in:json,pdf,excel,csv', // Format export report
            'include_charts' => 'nullable|boolean', // Sertakan grafik atau tidak
            'categories' => 'nullable|array', // Filter kategori
            'categories.*' => 'exists:categories,id', // Validasi setiap kategori ID
            'type' => 'nullable|in:income,expense,both', // Filter tipe transaksi
        ];
    }

    /**
     * Custom message untuk validasi report
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'start_date.date' => 'Format tanggal mulai tidak valid',
            'end_date.required' => 'Tanggal akhir wajib diisi',
            'end_date.date' => 'Format tanggal akhir tidak valid',
            'end_date.after_or_equal' => 'Tanggal akhir harus setelah atau sama dengan tanggal mulai',
            'format.in' => 'Format tidak valid. Pilih: json, pdf, excel, csv',
            'categories.*.exists' => 'Kategori tidak ditemukan',
            'type.in' => 'Tipe tidak valid. Pilih: income, expense, both',
        ];
    }

    /**
     * Set default value sebelum validasi berjalan
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'format' => $this->input('format', 'json'), // default JSON
            'include_charts' => $this->input('include_charts', true), // default true
            'type' => $this->input('type', 'both'), // default both
        ]);
    }
}