<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduledReportRequest extends FormRequest
{
    /**
     * Izinkan semua user untuk membuat scheduled report
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk scheduled report
     */
    public function rules(): array
    {
        return [
            'frequency' => 'required|in:weekly,monthly', // Frekuensi laporan
            'day_of_week' => 'required_if:frequency,weekly|nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday', // Hari untuk weekly report
            'day_of_month' => 'required_if:frequency,monthly|nullable|integer|between:1,28', // Tanggal untuk monthly report
            'send_time' => 'required|date_format:H:i', // Waktu pengiriman report
            'email' => 'required|email', // Email tujuan report
            'format' => 'required|in:pdf,excel', // Format file report
            'include_charts' => 'boolean', // Include grafik atau tidak
        ];
    }

    /**
     * Custom message untuk validasi scheduled report
     */
    public function messages(): array
    {
        return [
            'frequency.required' => 'Frekuensi wajib diisi',
            'frequency.in' => 'Frekuensi harus weekly atau monthly',
            'day_of_week.required_if' => 'Hari wajib dipilih untuk laporan mingguan',
            'day_of_month.required_if' => 'Tanggal wajib dipilih untuk laporan bulanan',
            'day_of_month.between' => 'Tanggal harus antara 1-28',
            'send_time.required' => 'Waktu kirim wajib diisi',
            'email.required' => 'Email tujuan wajib diisi',
            'email.email' => 'Format email tidak valid',
            'format.required' => 'Format laporan wajib dipilih',
            'format.in' => 'Format harus pdf atau excel',
        ];
    }
}