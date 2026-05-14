<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    /**
     * Transform resource menjadi array untuk response API
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // ID budget
            'category' => CategoryResource::make($this->whenLoaded('category')), // Relasi category (jika loaded)
            'month_year' => $this->month_year instanceof \Carbon\Carbon
                ? $this->month_year->format('Y-m') // Format jika Carbon instance
                : $this->month_year, // Raw value jika string
            'limit_amount' => (float) $this->limit_amount, // Limit budget
            'spent_amount' => (float) $this->spent_amount, // Total pengeluaran
            'remaining_amount' => (float) $this->remaining, // Sisa budget
            'percentage' => (float) $this->percentage, // Persentase penggunaan budget
            'is_overspent' => $this->isOverspent(), // Status apakah melebihi budget
            'status' => $this->status->value, // Status enum budget
        ];
    }
}