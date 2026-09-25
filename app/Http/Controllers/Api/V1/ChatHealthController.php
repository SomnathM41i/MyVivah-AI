<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ChatHealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            DB::selectOne('select 1');
        } catch (\Throwable) {
            return response()->json(['success' => false, 'data' => ['status' => 'degraded', 'checks' => ['database' => false]]], 503);
        }

        return response()->json(['success' => true, 'data' => ['status' => 'ok', 'checks' => ['database' => true]]]);
    }
}
