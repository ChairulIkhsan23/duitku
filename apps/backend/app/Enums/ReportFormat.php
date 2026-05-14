<?php

namespace App\Enums;

enum ReportFormat: string
{
    case PDF = 'pdf'; // Format laporan PDF
    case EXCEL = 'excel'; // Format laporan Excel
    
    public function label(): string
    {
        return match($this) {
            self::PDF => 'PDF', // Label PDF
            self::EXCEL => 'Excel', // Label Excel
        };
    }
    
    public function extension(): string
    {
        return match($this) {
            self::PDF => '.pdf', // Ekstensi file PDF
            self::EXCEL => '.xlsx', // Ekstensi file Excel
        };
    }
}