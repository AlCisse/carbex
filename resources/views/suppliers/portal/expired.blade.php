<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('carbex.supplier_portal.expired_title') }} - Carbex</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto p-6">
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-6">
                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-2">{{ __('carbex.supplier_portal.expired_title') }}</h1>
            <p class="text-gray-600 mb-4">
                {{ __('carbex.supplier_portal.expired_message', ['date' => $invitation->expires_at->format('d/m/Y')]) }}
            </p>
            <p class="text-sm text-gray-500 mb-6">
                {{ __('carbex.supplier_portal.expired_contact', ['organization' => $invitation->organization->name]) }}
            </p>

            @if($invitation->invitedBy)
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">{{ __('carbex.supplier_portal.contact_label') }}</p>
                <p class="font-medium text-gray-900">{{ $invitation->invitedBy->name }}</p>
                <a href="mailto:{{ $invitation->invitedBy->email }}" class="text-green-600 hover:text-green-700">
                    {{ $invitation->invitedBy->email }}
                </a>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
