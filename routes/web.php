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

    // Site Comparison (T174-T175)
    Route::get('/sites/comparison', App\Livewire\Sites\SiteComparison::class)
        ->name('sites.comparison');

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

    // Badge share page (public with token)
    Route::get('/badges/share/{token}', [\App\Http\Controllers\BadgeShareController::class, 'show'])
        ->name('badges.share')
        ->withoutMiddleware(['auth', 'verified']);

    // Promote (Badge Showcase, Sharing) - TrackZero-inspired
    Route::get('/promote/showcase', App\Livewire\Promote\BadgeShowcase::class)
        ->name('promote.showcase');

    Route::get('/promote/share', function () {
        return view('promote.share');
    })->name('promote.share');

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
    })->name('transition-plan.trajectory');

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

    // Logout
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

// Accept invitation (special route)
Route::get('/invitation/{token}', function (string $token) {
    return view('auth.accept-invitation', ['token' => $token]);
})->name('invitation.accept');

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
