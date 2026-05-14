<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BadgeResource extends JsonResource
{
    /**
     * Transform resource menjadi array untuk response API
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // ID badge
            'name' => $this->name, // Nama badge
            'slug' => $this->slug, // Slug unik badge
            'description' => $this->description, // Deskripsi badge
            'icon' => $this->icon, // Icon badge
            'color' => $this->color, // Warna badge
            'points' => $this->points, // Poin yang diberikan badge
            'awarded_at' => $this->pivot?->awarded_at?->format('Y-m-d H:i:s'), // Tanggal badge diperoleh (jika ada relasi pivot)
        ];
    }
}