<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @method Response authorize(string $ability, mixed $arguments = [])
 */
abstract class Controller
{
    use AuthorizesRequests;
}
