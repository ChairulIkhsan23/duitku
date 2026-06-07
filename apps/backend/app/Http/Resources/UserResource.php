<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform resource menjadi array untuk response API
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // ID user
            'name' => $this->name, // Nama user
            'email' => $this->email, // Email user
            'currency_code' => $this->currency_code, // Mata uang yang digunakan user
            'initial_balance' => (float) $this->initial_balance, // saldo awal
            'current_balance' => (float) $this->current_balance, // saldo saat ini
            'onboarding_template' => $this->onboarding_template, // template onboarding yang dipilih user
            'streak_days' => (int) ($this->streak_days ?? 0), // jumlah hari streak transaksi
            'is_premium' => (bool) ($this->is_premium ?? false), // status premium user
            'premium_until' => $this->premium_until?->format('Y-m-d'), // masa aktif premium
            'avatar' => $this->avatar, // avatar user
            'settings' => $this->settings, // pengaturan user (JSON)
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'), // waktu akun dibuat
        ];
    }
}