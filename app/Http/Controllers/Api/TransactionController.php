<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => []]);
    }

    public function pendingReview(): JsonResponse
    {
        return response()->json(['data' => []]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total' => 0,
            'pending' => 0,
            'categorized' => 0,
            'excluded' => 0,
        ]);
    }

    public function show(string $transaction): JsonResponse
    {
        return response()->json(['message' => 'Not found'], 404);
    }

    public function categorize(Request $request, string $transaction): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function validate(string $transaction): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function exclude(string $transaction): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function bulkCategorize(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function import(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
