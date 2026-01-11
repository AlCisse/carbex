{{-- Embedded Badge Widget - For iframe integration --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $badgeName }} - {{ $organization->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #ecfdf5 0%, #ccfbf1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .badge-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 280px;
        }
        .badge-header {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%);
            padding: 24px 16px;
            text-align: center;
        }
        .badge-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .badge-icon span {
            font-size: 32px;
        }
        .badge-icon svg {
            width: 36px;
            height: 36px;
            color: #059669;
        }
        .badge-name {
            color: white;
            font-size: 16px;
            font-weight: 700;
        }
        .badge-body {
            padding: 20px 16px;
            text-align: center;
        }
        .org-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .org-name {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
        }
        .org-country {
            font-size: 12px;
            color: #6b7280;
        }
        .badge-date {
            margin-top: 12px;
            font-size: 11px;
            color: #6b7280;
        }
        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #ecfdf5;
            color: #065f46;
            font-size: 10px;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 9999px;
            margin-top: 8px;
        }
        .verified-badge svg {
            width: 12px;
            height: 12px;
        }
        .badge-footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .carbex-logo {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #6b7280;
            text-decoration: none;
        }
        .carbex-logo:hover {
            color: #059669;
        }
        .carbex-logo-icon {
            width: 20px;
            height: 20px;
            background: #10b981;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .carbex-logo-icon svg {
            width: 12px;
            height: 12px;
            color: white;
        }
        .verify-link {
            font-size: 11px;
            color: #10b981;
            text-decoration: none;
        }
        .verify-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="badge-card">
        <div class="badge-header">
            <div class="badge-icon">
                @if($badge->icon)
                    <span>{{ $badge->icon }}</span>
                @else
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endif
            </div>
            <h1 class="badge-name">{{ $badgeName }}</h1>
        </div>

        <div class="badge-body">
            <p class="org-label">{{ __('carbex.promote.awarded_to') }}</p>
            <h2 class="org-name">{{ $organization->name }}</h2>
            @if($organization->country)
                <p class="org-country">{{ $organization->country }}</p>
            @endif

            <p class="badge-date">{{ __('carbex.promote.earned_on') }} {{ $earned_at->format('d/m/Y') }}</p>

            <span class="verified-badge">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                {{ __('carbex.promote.verified') }}
            </span>
        </div>

        <div class="badge-footer">
            <a href="{{ url('/') }}" target="_blank" class="carbex-logo">
                <span class="carbex-logo-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                    </svg>
                </span>
                Carbex
            </a>
            <a href="{{ route('badge.public', ['token' => $share_token]) }}" target="_blank" class="verify-link">
                {{ __('carbex.promote.verify') }}
            </a>
        </div>
    </div>
</body>
</html>
