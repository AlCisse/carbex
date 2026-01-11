<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

/**
 * Report Policy
 *
 * Authorization rules for reports:
 * - View: Organization members
 * - Create: Admin and Manager
 * - Delete: Admin only
 */
class ReportPolicy
{
    /**
     * Determine if the user can view any reports.
     */
    public function viewAny(User $user): bool
    {
        return true; // All organization members can list reports
    }

    /**
     * Determine if the user can view the report.
     */
    public function view(User $user, Report $report): bool
    {
        return $user->organization_id === $report->organization_id;
    }

    /**
     * Determine if the user can create reports.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine if the user can update the report.
     */
    public function update(User $user, Report $report): bool
    {
        return $user->organization_id === $report->organization_id
            && in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine if the user can delete the report.
     */
    public function delete(User $user, Report $report): bool
    {
        return $user->organization_id === $report->organization_id
            && $user->role === 'admin';
    }

    /**
     * Determine if the user can download the report.
     */
    public function download(User $user, Report $report): bool
    {
        return $this->view($user, $report);
    }
}
