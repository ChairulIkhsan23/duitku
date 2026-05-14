<?php

namespace App\Enums;

enum BudgetStatus: string
{
    case GOOD = 'good'; // Status budget masih aman
    case WARNING = 'warning'; // Status budget mendekati limit
    case OVERSPENT = 'overspent'; // Status budget sudah melebihi limit
}