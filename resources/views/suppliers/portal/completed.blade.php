<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('carbex.supplier_portal.completed_title') }} - Carbex</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-lg w-full mx-auto p-6">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <div class="text-center mb-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mb-2">{{ __('carbex.supplier_portal.completed_title') }}</h1>
                <p class="text-gray-600">
                    {{ __('carbex.supplier_portal.completed_message', ['date' => $invitation->completed_at->format('d/m/Y H:i')]) }}
                </p>
            </div>

            @if($emission)
            <div class="border-t pt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('carbex.supplier_portal.summary') }}</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">{{ __('carbex.supplier_portal.year') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $invitation->year }}</dd>
                    </div>
                    @if($emission->scope1_total)
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Scope 1</dt>
                        <dd class="font-medium text-gray-900">{{ number_format($emission->scope1_total, 2) }} tCO2e</dd>
                    </div>
                    @endif
                    @if($emission->scope2_market || $emission->scope2_location)
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Scope 2</dt>
                        <dd class="font-medium text-gray-900">{{ number_format($emission->scope2_market ?? $emission->scope2_location, 2) }} tCO2e</dd>
                    </div>
                    @endif
                    @if($emission->revenue)
                    <div class="flex justify-between">
                        <dt class="text-gray-600">{{ __('carbex.supplier_portal.revenue') }}</dt>
                        <dd class="font-medium text-gray-900">{{ number_format($emission->revenue, 0, ',', ' ') }} {{ $emission->revenue_currency }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-600">{{ __('carbex.supplier_portal.quality_score') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $emission->getQualityScore() }}/100</dd>
                    </div>
                </dl>
            </div>
            @endif

            <div class="mt-8 text-center text-sm text-gray-500">
                <p>{{ __('carbex.supplier_portal.modify_contact', ['organization' => $invitation->organization->name]) }}</p>
            </div>
        </div>
    </div>
</body>
</html>
