<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsightResource extends JsonResource
{
    /**
     * Transform resource menjadi array untuk response API
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // ID insight
            'period_type' => $this->period_type->value, // weekly / monthly / dll
            'period_start' => optional($this->period_start)->format('Y-m-d'), // awal periode
            'period_end' => optional($this->period_end)->format('Y-m-d'), // akhir periode
            'period_label' => $this->getPeriodLabel(), // label human readable periode
            'summary' => $this->data['summary'] ?? null, // ringkasan insight
            'insights' => $this->data['insights'] ?? [], // daftar insight
            'recommendations' => $this->data['recommendations'] ?? [], // rekomendasi user
            'is_read' => $this->is_read, // status sudah dibaca atau belum
            'generated_at' => optional($this->generated_at)->format('Y-m-d H:i:s'), // waktu generate
            'generated_at_human' => optional($this->generated_at)->diffForHumans(), // format manusia (e.g. 2 jam lalu)
            'insights_count' => count($this->data['insights'] ?? []), // total insight
        ];
    }

    /**
     * Ambil label periode dari enum
     */
    private function getPeriodLabel(): string
    {
        return $this->period_type->label();
    }
}