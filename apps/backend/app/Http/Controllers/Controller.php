<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Base controller utama untuk semua controller aplikasi
 * Menyediakan fitur authorization dan validation dari Laravel
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}