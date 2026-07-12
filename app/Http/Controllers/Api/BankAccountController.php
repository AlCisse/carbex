<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => []]);
    }

    public function update(Request $request, string $account): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function toggleSync(string $account): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
