<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform resource menjadi array untuk response API
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // ID notification
            'type' => $this->type, // Tipe notifikasi (budget, reminder, dll)
            'title' => $this->title, // Judul notifikasi
            'body' => $this->body, // Isi pesan notifikasi
            'data' => $this->data, // Data tambahan (JSON)
            'is_read' => $this->is_read, // Status sudah dibaca atau belum
            'read_at' => $this->read_at?->format('Y-m-d H:i:s'), // Waktu dibaca
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'), // Waktu dibuat
        ];
    }
}