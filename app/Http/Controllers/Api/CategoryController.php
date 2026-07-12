<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * GHG category codes (BEGES) mapped to their scope.
     *
     * @var array<string, int>
     */
    protected array $categories = [
        '1.1' => 1,
        '1.2' => 1,
        '1.4' => 1,
        '1.5' => 1,
        '2.1' => 2,
        '3.1' => 3,
        '3.2' => 3,
        '3.3' => 3,
        '3.5' => 3,
        '4.1' => 3,
        '4.2' => 3,
        '4.3' => 3,
        '4.4' => 3,
        '4.5' => 3,
    ];

    public function index(): JsonResponse
    {
        $data = collect($this->categories)
            ->map(fn (int $scope, string $code) => [
                'id' => $code,
                'scope' => $scope,
                'name' => __('linscarbon.emissions.categories.' . str_replace('.', '_', $code)),
            ])
            ->values();

        return response()->json(['data' => $data]);
    }

    public function emissionFactors(Request $request): JsonResponse
    {
        // TODO: Return emission factors from database
        return response()->json([
            'data' => [],
            'meta' => [
                'total' => 0,
                'per_page' => 20,
                'current_page' => 1,
            ],
        ]);
    }
}
