<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\SupplierInvitation;
use App\Services\Suppliers\SupplierDataValidator;
use App\Services\Suppliers\SupplierInvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Supplier Portal Controller
 *
 * Handles the public-facing supplier portal where suppliers
 * can submit their emission data.
 */
class SupplierPortalController extends Controller
{
    public function __construct(
        private SupplierInvitationService $invitationService,
        private SupplierDataValidator $validator
    ) {}

    /**
     * Show the supplier portal landing page.
     *
     * GET /supplier-portal/{token}
     */
    public function show(string $token): View
    {
        $invitation = $this->invitationService->findByToken($token);

        if (!$invitation) {
            return view('suppliers.portal.not-found');
        }

        if ($invitation->isExpired()) {
            return view('suppliers.portal.expired', [
                'invitation' => $invitation,
            ]);
        }

        if ($invitation->isCompleted()) {
            return view('suppliers.portal.completed', [
                'invitation' => $invitation,
                'emission' => $invitation->emission,
            ]);
        }

        // Mark as opened
        $this->invitationService->accessPortal($invitation);

        return view('suppliers.portal.form', [
            'invitation' => $invitation,
            'supplier' => $invitation->supplier,
            'organization' => $invitation->organization,
            'requestedData' => $invitation->requested_data,
            'existingData' => $invitation->supplier->emissionForYear($invitation->year),
        ]);
    }

    /**
     * Submit emission data.
     *
     * POST /supplier-portal/{token}/submit
     */
    public function submit(Request $request, string $token): JsonResponse
    {
        $invitation = $this->invitationService->findByToken($token);

        if (!$invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Invitation not found.',
            ], 404);
        }

        if (!$invitation->isActive()) {
            return response()->json([
                'success' => false,
                'message' => $invitation->isExpired()
                    ? 'This invitation has expired.'
                    : 'This invitation is no longer active.',
            ], 400);
        }

        // Validate the data
        $validated = $request->validate([
            // Scope 1
            'scope1_total' => 'nullable|numeric|min:0',
            'scope1_breakdown' => 'nullable|array',
            'scope1_breakdown.stationary_combustion' => 'nullable|numeric|min:0',
            'scope1_breakdown.mobile_combustion' => 'nullable|numeric|min:0',
            'scope1_breakdown.fugitive_emissions' => 'nullable|numeric|min:0',
            'scope1_breakdown.process_emissions' => 'nullable|numeric|min:0',

            // Scope 2
            'scope2_location' => 'nullable|numeric|min:0',
            'scope2_market' => 'nullable|numeric|min:0',
            'scope2_breakdown' => 'nullable|array',
            'scope2_breakdown.electricity' => 'nullable|numeric|min:0',
            'scope2_breakdown.heat' => 'nullable|numeric|min:0',
            'scope2_breakdown.steam' => 'nullable|numeric|min:0',
            'scope2_breakdown.cooling' => 'nullable|numeric|min:0',

            // Scope 3 (optional)
            'scope3_total' => 'nullable|numeric|min:0',
            'scope3_breakdown' => 'nullable|array',

            // Company info
            'revenue' => 'nullable|numeric|min:0',
            'revenue_currency' => 'nullable|string|size:3',
            'employees' => 'nullable|integer|min:1',

            // Verification
            'verification_standard' => 'nullable|string|max:255',
            'verifier_name' => 'nullable|string|max:255',
            'verification_date' => 'nullable|date',
            'uncertainty_percent' => 'nullable|numeric|min:0|max:100',

            // Methodology
            'methodology' => 'nullable|array',
            'methodology.calculation_approach' => 'nullable|string',
            'methodology.data_sources' => 'nullable|array',
            'methodology.assumptions' => 'nullable|string',

            // Notes
            'notes' => 'nullable|string|max:5000',
        ]);

        // Additional validation
        $validationResult = $this->validator->validate($validated, $invitation->requested_data);

        if (!$validationResult['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validationResult['errors'],
            ], 422);
        }

        try {
            $emission = $this->invitationService->submitEmissionData($invitation, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your emission data has been submitted successfully.',
                'data' => [
                    'emission_id' => $emission->id,
                    'quality_score' => $emission->getQualityScore(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving your data. Please try again.',
            ], 500);
        }
    }

    /**
     * Get portal status (for AJAX polling).
     *
     * GET /supplier-portal/{token}/status
     */
    public function status(string $token): JsonResponse
    {
        $invitation = $this->invitationService->findByToken($token);

        if (!$invitation) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'status' => $invitation->status,
            'is_expired' => $invitation->isExpired(),
            'expires_at' => $invitation->expires_at->toIso8601String(),
            'is_completed' => $invitation->isCompleted(),
        ]);
    }

    /**
     * Request extension.
     *
     * POST /supplier-portal/{token}/extend
     */
    public function requestExtension(Request $request, string $token): JsonResponse
    {
        $invitation = $this->invitationService->findByToken($token);

        if (!$invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Invitation not found.',
            ], 404);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // For now, auto-extend by 14 days (could be made into a request workflow)
        $invitation->extend(14);

        // TODO: Notify organization about extension request

        return response()->json([
            'success' => true,
            'message' => 'Your deadline has been extended.',
            'new_expires_at' => $invitation->expires_at->toIso8601String(),
        ]);
    }

    /**
     * Download data template.
     *
     * GET /supplier-portal/{token}/template
     */
    public function downloadTemplate(string $token): JsonResponse
    {
        $invitation = $this->invitationService->findByToken($token);

        if (!$invitation) {
            return response()->json(['error' => 'Not found'], 404);
        }

        // Return a JSON template that can be used to fill in data
        $template = [
            'year' => $invitation->year,
            'supplier' => [
                'name' => $invitation->supplier->name,
                'country' => $invitation->supplier->country,
            ],
            'requested_fields' => $invitation->requested_data,
            'emission_data' => [
                'scope1' => [
                    'total' => null,
                    'breakdown' => [
                        'stationary_combustion' => null,
                        'mobile_combustion' => null,
                        'fugitive_emissions' => null,
                        'process_emissions' => null,
                    ],
                ],
                'scope2' => [
                    'location_based' => null,
                    'market_based' => null,
                    'breakdown' => [
                        'electricity' => null,
                        'heat' => null,
                        'steam' => null,
                        'cooling' => null,
                    ],
                ],
                'scope3' => [
                    'total' => null,
                    'breakdown' => [],
                ],
            ],
            'company_info' => [
                'revenue' => null,
                'revenue_currency' => 'EUR',
                'employees' => null,
            ],
            'verification' => [
                'standard' => null,
                'verifier_name' => null,
                'date' => null,
            ],
            'methodology' => [
                'calculation_approach' => null,
                'data_sources' => [],
                'assumptions' => null,
            ],
            'notes' => null,
        ];

        return response()->json($template);
    }
}
