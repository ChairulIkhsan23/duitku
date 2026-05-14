<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform resource menjadi array untuk response API
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // ID kategori
            'name' => $this->name, // Nama kategori
            'type' => $this->type, // income / expense
            'icon' => $this->icon, // Icon kategori
            'color' => $this->color, // Warna kategori
            'budget_default' => $this->budget_default 
                ? (float) $this->budget_default // Default budget jika ada
                : null,
            'is_default' => $this->is_default, // Apakah kategori bawaan sistem
            'user_id' => $this->user_id, // Owner kategori (null jika global)
        ];
    }
}