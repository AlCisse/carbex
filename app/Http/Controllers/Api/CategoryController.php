<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        // TODO: Return emission categories
        return response()->json([
            'data' => [
                ['id' => '1.1', 'scope' => 1, 'name' => 'Sources fixes de combustion'],
                ['id' => '1.2', 'scope' => 1, 'name' => 'Sources mobiles de combustion'],
                ['id' => '1.4', 'scope' => 1, 'name' => 'Émissions fugitives'],
                ['id' => '1.5', 'scope' => 1, 'name' => 'Biomasse (sols et forêts)'],
                ['id' => '2.1', 'scope' => 2, 'name' => 'Consommation d\'électricité'],
                ['id' => '3.1', 'scope' => 3, 'name' => 'Transport de marchandise amont'],
                ['id' => '3.2', 'scope' => 3, 'name' => 'Transport de marchandise aval'],
                ['id' => '3.3', 'scope' => 3, 'name' => 'Déplacements domicile-travail'],
                ['id' => '3.5', 'scope' => 3, 'name' => 'Déplacements professionnels'],
                ['id' => '4.1', 'scope' => 3, 'name' => 'Achats de biens'],
                ['id' => '4.2', 'scope' => 3, 'name' => 'Immobilisations de biens'],
                ['id' => '4.3', 'scope' => 3, 'name' => 'Gestion des déchets'],
                ['id' => '4.4', 'scope' => 3, 'name' => 'Actifs en leasing amont'],
                ['id' => '4.5', 'scope' => 3, 'name' => 'Achats de services'],
            ],
        ]);
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
