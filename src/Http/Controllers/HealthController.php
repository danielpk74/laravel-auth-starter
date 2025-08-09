<?php

namespace Danielpk74\LaravelAuthStarter\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HealthController extends \Illuminate\Routing\Controller
{
    /**
     * Health check endpoint
     */
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'service' => 'Laravel Application',
        ]);
    }
}
