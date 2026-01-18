<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes for Carbex web application (Livewire-based)
| Authentication is handled by Livewire components
|
*/

// Language switch
Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['fr', 'en', 'de'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('language.switch');

// Public pages
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/tarifs', function () {
    return view('marketing.pricing');
})->name('pricing');

Route::get('/pour-qui', function () {
    return view('marketing.pour-qui');
})->name('pour-qui');

Route::get('/contact', function () {
    return view('marketing.contact');
})->name('contact');

// Blog routes
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// Legal pages
Route::get('/cgv', fn () => view('marketing.legal.cgv'))->name('cgv');
Route::get('/cgu', fn () => view('marketing.legal.cgu'))->name('cgu');
Route::get('/mentions-legales', fn () => view('marketing.legal.mentions'))->name('mentions-legales');
Route::get('/nos-engagements', fn () => view('marketing.legal.engagements'))->name('engagements');
Route::get('/confidentialite', fn () => view('marketing.legal.confidentialite'))->name('confidentialite');

// SEO
Route::get('/sitemap.xml', [\App\Http\Controllers\SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SeoController::class, 'robots'])->name('robots');

// Guest routes (unauthenticated users)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
});

// Email verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (string $id, string $hash) {
    $user = \App\Models\User::findOrFail($id);

    if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        throw new \Illuminate\Auth\Access\AuthorizationException;
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    return redirect()->route('dashboard')->with('verified', true);
})->middleware(['signed'])->name('verification.verify');

// Onboarding (auth required, but no email verification)
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', App\Livewire\Onboarding\OnboardingWizard::class)
        ->name('onboarding');

    // Logout (must be accessible to unverified users too)
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (Livewire full-page component)
    Route::get('/dashboard', App\Livewire\Dashboard\DashboardPage::class)->name('dashboard');

    // Organization settings
    Route::get('/settings', function () {
        return view('settings.organization');
    })->name('settings');

    Route::get('/settings/profile', function () {
        return view('settings.profile');
    })->name('settings.profile');

    Route::get('/settings/team', function () {
        return view('settings.team');
    })->name('settings.team');

    Route::get('/settings/sites', function () {
        return view('settings.sites');
    })->name('settings.sites');

    Route::get('/settings/ai', function () {
        return view('settings.ai');
    })->name('settings.ai');

    // Site Comparison (T174-T175)
    Route::get('/sites/comparison', App\Livewire\Sites\SiteComparison::class)
        ->name('sites.comparison');

    // Site Import (T176)
    Route::get('/sites/import', App\Livewire\Sites\SiteImport::class)
        ->name('sites.import');

    // Employee Engagement (T180-T182)
    Route::get('/engage/employees', App\Livewire\Engage\EmployeeEngagement::class)
        ->name('engage.employees');

    Route::get('/settings/billing', App\Livewire\Billing\SubscriptionManager::class)
        ->name('settings.billing');

    // Banking
    Route::get('/banking', function () {
        return view('banking.index');
    })->name('banking');

    Route::get('/banking/connect', function () {
        return view('banking.connect');
    })->name('banking.connect');

    Route::get('/banking/callback', function () {
        return view('banking.callback');
    })->name('banking.callback');

    // Transactions
    Route::get('/transactions', function () {
        return view('transactions.index');
    })->name('transactions.index');

    Route::get('/transactions/review', function () {
        return view('transactions.review');
    })->name('transactions.review');

    Route::get('/transactions/import', function () {
        return view('transactions.import');
    })->name('transactions.import');

    // Documents (AI extraction)
    Route::get('/documents', function () {
        return view('documents.index');
    })->name('documents');

    // Emissions / Bilan Carbone
    Route::get('/emissions', function () {
        return view('emissions.index');
    })->name('emissions');

    Route::get('/emissions/scope/{scope}', function (int $scope) {
        return view('emissions.scope', ['scope' => $scope]);
    })->name('emissions.scope');

    Route::get('/emissions/{scope}/{category}', function (int $scope, string $category) {
        return view('emissions.category', ['scope' => $scope, 'category' => $category]);
    })->name('emissions.category');

    Route::get('/emissions/activities', function () {
        return view('emissions.activities');
    })->name('emissions.activities');

    // AI Analysis (Analyse IA)
    Route::get('/ai-analysis', function () {
        return view('ai.analysis');
    })->name('ai.analysis');

    // Suppliers (Fournisseurs Scope 3)
    Route::get('/suppliers', App\Livewire\Suppliers\SupplierManagement::class)
        ->name('suppliers');

    // Gamification (Badges & Engagement)
    Route::get('/gamification', function () {
        return view('gamification.index');
    })->name('gamification');

    // Badge Showcase (T169-T172)
    Route::get('/promote/badges', App\Livewire\Promote\BadgeShowcase::class)
        ->name('promote.badges');

    // Compliance Monitor (T177-T179)
    Route::get('/compliance', App\Livewire\Compliance\ComplianceMonitor::class)
        ->name('compliance');

    // CSRD Compliance (EU Corporate Sustainability Reporting Directive)
    Route::prefix('csrd')->group(function () {
        Route::get('/', App\Livewire\Csrd\CsrdDashboard::class)
            ->name('csrd.dashboard');
        Route::get('/esrs2', App\Livewire\Csrd\Esrs2DisclosureManager::class)
            ->name('csrd.esrs2');
        Route::get('/transition-plan', App\Livewire\Csrd\ClimateTransitionPlanEditor::class)
            ->name('csrd.transition-plan');
        Route::get('/taxonomy', App\Livewire\Csrd\EuTaxonomyReportEditor::class)
            ->name('csrd.taxonomy');
        Route::get('/due-diligence', App\Livewire\Csrd\ValueChainDueDiligenceEditor::class)
            ->name('csrd.due-diligence');
    });

    // Plan de transition
    Route::get('/transition-plan', function () {
        return view('transition-plan.index');
    })->name('transition-plan');

    Route::get('/transition-plan/actions', function () {
        return view('transition-plan.actions');
    })->name('transition-plan.actions');

    // Trajectory (SBTi)
    Route::get('/trajectory', function () {
        return view('transition-plan.trajectory');
    })->name('trajectory');

    // Assessments (Bilans)
    Route::get('/assessments', function () {
        return view('assessments.index');
    })->name('assessments');

    Route::get('/assessments/switch/{year}', function (int $year) {
        session(['current_assessment_year' => $year]);
        return redirect()->back();
    })->name('assessments.switch');

    // Billing
    Route::get('/billing', function () {
        return view('billing.index');
    })->name('billing');

    // Reports (Livewire full-page component)
    Route::get('/reports', App\Livewire\Reports\ReportList::class)->name('reports');
    Route::get('/reports/{report}/download', function (App\Models\Report $report) {
        if ($report->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }
        if (!$report->isReady()) {
            abort(404);
        }
        $report->recordDownload();
        return \Illuminate\Support\Facades\Storage::download($report->file_path, basename($report->file_path));
    })->name('reports.download');
});

// Accept invitation (special route)
Route::get('/invitation/{token}', function (string $token) {
    return view('auth.accept-invitation', ['token' => $token]);
})->name('invitation.accept');

/*
|--------------------------------------------------------------------------
| Public Badge Routes
|--------------------------------------------------------------------------
*/
Route::get('/badge/{token}', [\App\Http\Controllers\BadgeShareController::class, 'show'])
    ->name('badge.public');
Route::get('/badge/{token}/verify', [\App\Http\Controllers\BadgeShareController::class, 'verify'])
    ->name('badge.verify');

/*
|--------------------------------------------------------------------------
| Supplier Portal Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('supplier-portal')->group(function () {
    Route::get('/{token}', [\App\Http\Controllers\Suppliers\SupplierPortalController::class, 'show'])
        ->name('supplier.portal');
    Route::post('/{token}/submit', [\App\Http\Controllers\Suppliers\SupplierPortalController::class, 'submit'])
        ->name('supplier.portal.submit');
    Route::get('/{token}/status', [\App\Http\Controllers\Suppliers\SupplierPortalController::class, 'status'])
        ->name('supplier.portal.status');
    Route::post('/{token}/extend', [\App\Http\Controllers\Suppliers\SupplierPortalController::class, 'requestExtension'])
        ->name('supplier.portal.extend');
    Route::get('/{token}/template', [\App\Http\Controllers\Suppliers\SupplierPortalController::class, 'downloadTemplate'])
        ->name('supplier.portal.template');
});
