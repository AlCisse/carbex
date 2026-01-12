<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('carbex.supplier_portal.not_found_title') }} - Carbex</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto p-6">
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-2">{{ __('carbex.supplier_portal.not_found_title') }}</h1>
            <p class="text-gray-600 mb-6">
                {{ __('carbex.supplier_portal.not_found_message') }}
            </p>
            <p class="text-sm text-gray-500">
                {{ __('carbex.supplier_portal.not_found_contact') }}
            </p>
        </div>
    </div>
</body>
</html>
