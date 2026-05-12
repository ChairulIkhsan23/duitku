<?php

namespace App\Enums;

enum ReportFormat: string
{
    case PDF = 'pdf';
    case EXCEL = 'excel';
    
    public function label(): string
    {
        return match($this) {
            self::PDF => 'PDF',
            self::EXCEL => 'Excel',
        };
    }
    
    public function extension(): string
    {
        return match($this) {
            self::PDF => '.pdf',
            self::EXCEL => '.xlsx',
        };
    }
}