<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function jsonSuccess(): JsonResponse {
        return response()->json(['message' => 'Success']);
    }
}
