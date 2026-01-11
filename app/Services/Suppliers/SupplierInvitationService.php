<?php

namespace App\Services\Suppliers;

use App\Models\Organization;
use App\Models\Supplier;
use App\Models\SupplierEmission;
use App\Models\SupplierInvitation;
use App\Models\User;
use App\Notifications\SupplierInvitationNotification;
use App\Notifications\SupplierReminderNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SupplierInvitationService
{
    /**
     * Create a new supplier.
     */
    public function createSupplier(Organization $organization, array $data): Supplier
    {
        return Supplier::create([
            'organization_id' => $organization->id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'contact_name' => $data['contact_name'] ?? null,
            'contact_email' => $data['contact_email'] ?? $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'country' => $data['country'] ?? 'FR',
            'business_id' => $data['business_id'] ?? null,
            'sector' => $data['sector'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'categories' => $data['categories'] ?? null,
            'annual_spend' => $data['annual_spend'] ?? null,
            'currency' => $data['currency'] ?? 'EUR',
            'status' => Supplier::STATUS_PENDING,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Update a supplier.
     */
    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);

        return $supplier->fresh();
    }

    /**
     * Create and send an invitation.
     */
    public function invite(
        Supplier $supplier,
        User $invitedBy,
        int $year,
        array $options = []
    ): SupplierInvitation {
        return DB::transaction(function () use ($supplier, $invitedBy, $year, $options) {
            // Cancel any existing pending invitations for the same year
            $this->cancelPendingInvitations($supplier, $year);

            // Create new invitation
            $invitation = SupplierInvitation::create([
                'supplier_id' => $supplier->id,
                'organization_id' => $supplier->organization_id,
                'invited_by' => $invitedBy->id,
                'email' => $options['email'] ?? $supplier->contact_email ?? $supplier->email,
                'year' => $year,
                'requested_data' => $options['requested_data'] ?? SupplierInvitation::DEFAULT_REQUESTED_DATA,
                'expires_at' => $options['expires_at'] ?? now()->addDays(30),
                'message' => $options['message'] ?? null,
                'status' => SupplierInvitation::STATUS_PENDING,
            ]);

            // Update supplier status
            $supplier->update(['status' => Supplier::STATUS_INVITED]);

            // Send notification
            if ($options['send_email'] ?? true) {
                $this->sendInvitation($invitation);
            }

            return $invitation;
        });
    }

    /**
     * Send invitation email.
     */
    public function sendInvitation(SupplierInvitation $invitation): void
    {
        Notification::route('mail', $invitation->email)
            ->notify(new SupplierInvitationNotification($invitation));

        $invitation->markAsSent();
    }

    /**
     * Send reminder email.
     */
    public function sendReminder(SupplierInvitation $invitation): void
    {
        if (!$invitation->isActive()) {
            return;
        }

        Notification::route('mail', $invitation->email)
            ->notify(new SupplierReminderNotification($invitation));

        $invitation->recordReminder();
    }

    /**
     * Process bulk invitations.
     */
    public function bulkInvite(
        Organization $organization,
        array $supplierIds,
        User $invitedBy,
        int $year,
        array $options = []
    ): Collection {
        $results = collect();

        foreach ($supplierIds as $supplierId) {
            $supplier = Supplier::where('id', $supplierId)
                ->where('organization_id', $organization->id)
                ->first();

            if (!$supplier) {
                $results->push([
                    'supplier_id' => $supplierId,
                    'success' => false,
                    'error' => 'Supplier not found',
                ]);
                continue;
            }

            if (!$supplier->contact_email && !$supplier->email) {
                $results->push([
                    'supplier_id' => $supplierId,
                    'success' => false,
                    'error' => 'No email address',
                ]);
                continue;
            }

            try {
                $invitation = $this->invite($supplier, $invitedBy, $year, $options);
                $results->push([
                    'supplier_id' => $supplierId,
                    'success' => true,
                    'invitation_id' => $invitation->id,
                ]);
            } catch (\Exception $e) {
                $results->push([
                    'supplier_id' => $supplierId,
                    'success' => false,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Cancel pending invitations for a supplier and year.
     */
    public function cancelPendingInvitations(Supplier $supplier, int $year): int
    {
        return SupplierInvitation::where('supplier_id', $supplier->id)
            ->where('year', $year)
            ->pending()
            ->update(['status' => SupplierInvitation::STATUS_CANCELLED]);
    }

    /**
     * Find invitation by token.
     */
    public function findByToken(string $token): ?SupplierInvitation
    {
        return SupplierInvitation::where('token', $token)
            ->with(['supplier', 'organization'])
            ->first();
    }

    /**
     * Access portal (mark as opened).
     */
    public function accessPortal(SupplierInvitation $invitation): SupplierInvitation
    {
        $invitation->markAsOpened();

        return $invitation;
    }

    /**
     * Submit emission data through portal.
     */
    public function submitEmissionData(
        SupplierInvitation $invitation,
        array $data
    ): SupplierEmission {
        return DB::transaction(function () use ($invitation, $data) {
            // Create or update emission record
            $emission = SupplierEmission::updateOrCreate(
                [
                    'supplier_id' => $invitation->supplier_id,
                    'year' => $invitation->year,
                ],
                [
                    'organization_id' => $invitation->organization_id,
                    'invitation_id' => $invitation->id,
                    'scope1_total' => $data['scope1_total'] ?? null,
                    'scope1_breakdown' => $data['scope1_breakdown'] ?? null,
                    'scope2_location' => $data['scope2_location'] ?? null,
                    'scope2_market' => $data['scope2_market'] ?? null,
                    'scope2_breakdown' => $data['scope2_breakdown'] ?? null,
                    'scope3_total' => $data['scope3_total'] ?? null,
                    'scope3_breakdown' => $data['scope3_breakdown'] ?? null,
                    'revenue' => $data['revenue'] ?? null,
                    'revenue_currency' => $data['revenue_currency'] ?? 'EUR',
                    'employees' => $data['employees'] ?? null,
                    'data_source' => SupplierEmission::SOURCE_SUPPLIER_REPORTED,
                    'verification_standard' => $data['verification_standard'] ?? null,
                    'verifier_name' => $data['verifier_name'] ?? null,
                    'verification_date' => $data['verification_date'] ?? null,
                    'uncertainty_percent' => $data['uncertainty_percent'] ?? null,
                    'methodology' => $data['methodology'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'submitted_at' => now(),
                ]
            );

            // Calculate emission intensity
            if ($emission->revenue && $emission->revenue > 0) {
                $emission->update([
                    'emission_intensity' => $emission->calculateIntensity(),
                ]);
            }

            // Mark invitation as completed
            $invitation->markAsCompleted();

            // Update supplier status
            $invitation->supplier->update([
                'status' => Supplier::STATUS_ACTIVE,
                'data_quality' => Supplier::QUALITY_SUPPLIER_SPECIFIC,
            ]);

            return $emission;
        });
    }

    /**
     * Get invitations needing reminders.
     */
    public function getInvitationsNeedingReminders(int $daysSinceLastReminder = 7): Collection
    {
        return SupplierInvitation::needsReminder($daysSinceLastReminder)
            ->with(['supplier', 'organization'])
            ->get();
    }

    /**
     * Process expired invitations.
     */
    public function processExpiredInvitations(): int
    {
        return SupplierInvitation::whereIn('status', [
            SupplierInvitation::STATUS_PENDING,
            SupplierInvitation::STATUS_SENT,
            SupplierInvitation::STATUS_OPENED,
        ])
            ->where('expires_at', '<=', now())
            ->update(['status' => SupplierInvitation::STATUS_EXPIRED]);
    }

    /**
     * Get invitation statistics for organization.
     */
    public function getStatistics(Organization $organization, int $year): array
    {
        $invitations = SupplierInvitation::where('organization_id', $organization->id)
            ->where('year', $year)
            ->get();

        return [
            'total' => $invitations->count(),
            'pending' => $invitations->whereIn('status', ['pending', 'sent'])->count(),
            'opened' => $invitations->where('status', 'opened')->count(),
            'completed' => $invitations->where('status', 'completed')->count(),
            'expired' => $invitations->where('status', 'expired')->count(),
            'response_rate' => $invitations->count() > 0
                ? round($invitations->where('status', 'completed')->count() / $invitations->count() * 100, 1)
                : 0,
        ];
    }

    /**
     * Get suppliers summary for organization.
     */
    public function getSuppliersSummary(Organization $organization, int $year): array
    {
        $suppliers = Supplier::where('organization_id', $organization->id)->get();

        $withData = $suppliers->filter(fn ($s) => $s->hasEmissionData($year))->count();
        $totalSpend = $suppliers->sum('annual_spend');

        return [
            'total_suppliers' => $suppliers->count(),
            'with_data' => $withData,
            'without_data' => $suppliers->count() - $withData,
            'data_coverage' => $suppliers->count() > 0
                ? round($withData / $suppliers->count() * 100, 1)
                : 0,
            'total_annual_spend' => $totalSpend,
            'by_status' => [
                'pending' => $suppliers->where('status', 'pending')->count(),
                'invited' => $suppliers->where('status', 'invited')->count(),
                'active' => $suppliers->where('status', 'active')->count(),
                'inactive' => $suppliers->where('status', 'inactive')->count(),
            ],
            'by_data_quality' => [
                'none' => $suppliers->where('data_quality', 'none')->count(),
                'estimated' => $suppliers->where('data_quality', 'estimated')->count(),
                'supplier_specific' => $suppliers->where('data_quality', 'supplier_specific')->count(),
                'verified' => $suppliers->where('data_quality', 'verified')->count(),
            ],
        ];
    }
}
