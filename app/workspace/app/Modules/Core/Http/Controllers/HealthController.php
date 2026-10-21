<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Application\Services\Health\HealthCheckService;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function ready(HealthCheckService $health): JsonResponse
    {
        $report = $health->report();

        return response()->json($report, $report['status'] === 'ok' ? 200 : 503, [
            'Cache-Control' => 'no-store',
        ]);
    }
}
