{{-- Email Signature HTML Template --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif;">
    <table cellpadding="0" cellspacing="0" border="0" style="margin-top: 20px; border-collapse: collapse;">
        <tr>
            <td style="padding: 12px 16px; background-color: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0;">
                <table cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle; padding-right: 12px;">
                            {{-- Badge Icon --}}
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                @if(isset($badge['icon']) && $badge['icon'])
                                    <span style="font-size: 24px; line-height: 48px; text-align: center; display: block; width: 48px;">{{ $badge['icon'] }}</span>
                                @else
                                    <img src="{{ asset('images/badge-default.png') }}" alt="Badge" width="28" height="28" style="display: block;">
                                @endif
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            <p style="margin: 0 0 2px 0; font-size: 13px; font-weight: 600; color: #065f46;">
                                🏆 {{ $badge['name'] ?? 'Carbon Badge' }}
                            </p>
                            <p style="margin: 0 0 4px 0; font-size: 11px; color: #047857;">
                                {{ $organization->name ?? 'Our Organization' }}
                            </p>
                            <table cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                                <tr>
                                    <td>
                                        <span style="display: inline-block; background-color: #dcfce7; color: #166534; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 500;">
                                            ✓ Verified by Carbex
                                        </span>
                                    </td>
                                    <td style="padding-left: 8px;">
                                        <a href="{{ $shareUrl ?? url('/') }}" target="_blank" style="font-size: 10px; color: #10b981; text-decoration: none;">
                                            View Badge →
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 8px; text-align: left;">
                <a href="{{ url('/') }}" target="_blank" style="display: inline-flex; align-items: center; text-decoration: none;">
                    <span style="display: inline-block; width: 16px; height: 16px; background-color: #10b981; border-radius: 3px; margin-right: 4px; text-align: center; line-height: 16px;">
                        <span style="color: white; font-size: 10px;">⚡</span>
                    </span>
                    <span style="font-size: 11px; color: #6b7280;">Powered by </span>
                    <span style="font-size: 11px; color: #10b981; font-weight: 600; margin-left: 2px;">Carbex</span>
                </a>
            </td>
        </tr>
    </table>
</body>
</html>
