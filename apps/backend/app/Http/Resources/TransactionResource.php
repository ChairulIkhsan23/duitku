<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform resource menjadi array untuk response API
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // ID transaksi
            'amount' => (float) $this->amount, // nominal transaksi (raw)
            'formatted_amount' => $this->formatted_amount, // nominal format rupiah / currency
            'type' => $this->type, // income / expense
            'date' => $this->date?->format('Y-m-d'), // tanggal transaksi
            'note' => $this->note, // catatan transaksi
            'photo_url' => $this->photo_url, // bukti transaksi (gambar)
            'location_name' => $this->location_name, // lokasi transaksi
            'is_duplicate' => $this->is_duplicate, // deteksi transaksi duplikat
            'category' => new CategoryResource(
                $this->whenLoaded('category')
            ), // relasi kategori
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'), // waktu dibuat
        ];
    }
}