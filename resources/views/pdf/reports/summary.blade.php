<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('carbex.reports.pdf.carbon_footprint_report') }} - {{ $report['organization']['name'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #1f2937;
        }
        .container {
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #10b981;
        }
        .header h1 {
            font-size: 24pt;
            color: #10b981;
            margin-bottom: 5px;
        }
        .header .subtitle {
            font-size: 14pt;
            color: #6b7280;
        }
        .header .period {
            font-size: 12pt;
            color: #374151;
            margin-top: 10px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14pt;
            color: #10b981;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .summary-card .value {
            font-size: 20pt;
            font-weight: bold;
            color: #111827;
        }
        .summary-card .unit {
            font-size: 10pt;
            color: #6b7280;
        }
        .summary-card .label {
            font-size: 9pt;
            color: #6b7280;
            margin-top: 5px;
        }
        .scope-1 { border-top: 3px solid #10b981; }
        .scope-2 { border-top: 3px solid #3b82f6; }
        .scope-3 { border-top: 3px solid #8b5cf6; }
        .total { border-top: 3px solid #111827; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f3f4f6;
            font-weight: 600;
            font-size: 9pt;
            color: #374151;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8pt;
            font-weight: 600;
        }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }
        .trend-up { color: #dc2626; }
        .trend-down { color: #16a34a; }
        .methodology-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .methodology-box h4 {
            color: #166534;
            margin-bottom: 10px;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>{{ __('carbex.reports.pdf.carbon_footprint_report') }}</h1>
            <div class="subtitle">{{ $report['organization']['name'] }}</div>
            <div class="period">{{ $report['report']['period']['label'] }}</div>
        </div>

        {{-- Executive Summary --}}
        <div class="section">
            <h2 class="section-title">{{ __('carbex.reports.pdf.executive_summary') }}</h2>

            <div class="summary-grid">
                <div class="summary-card total">
                    <div class="value">{{ number_format($report['summary']['total_emissions']['tonnes'], 1) }}</div>
                    <div class="unit">t CO₂e</div>
                    <div class="label">{{ __('carbex.dashboard.total_emissions') }}</div>
                </div>
                <div class="summary-card scope-1">
                    <div class="value">{{ number_format($report['summary']['scope_1']['tonnes'], 2) }}</div>
                    <div class="unit">t CO₂e</div>
                    <div class="label">Scope 1 ({{ $report['summary']['scope_1']['percent'] }}%)</div>
                </div>
                <div class="summary-card scope-2">
                    <div class="value">{{ number_format($report['summary']['scope_2']['tonnes'], 2) }}</div>
                    <div class="unit">t CO₂e</div>
                    <div class="label">Scope 2 ({{ $report['summary']['scope_2']['percent'] }}%)</div>
                </div>
                <div class="summary-card scope-3">
                    <div class="value">{{ number_format($report['summary']['scope_3']['tonnes'], 2) }}</div>
                    <div class="unit">t CO₂e</div>
                    <div class="label">Scope 3 ({{ $report['summary']['scope_3']['percent'] }}%)</div>
                </div>
            </div>

            {{-- Comparison with previous period --}}
            @if(isset($report['comparison']))
                <p>
                    {{ __('carbex.reports.pdf.compared_to_previous') }}:
                    <strong class="{{ $report['comparison']['change_direction'] === 'decrease' ? 'trend-down' : 'trend-up' }}">
                        @if($report['comparison']['change_direction'] === 'decrease')
                            ↓ {{ abs($report['comparison']['change_percent']) }}% {{ __('carbex.reports.pdf.reduction') }}
                        @elseif($report['comparison']['change_direction'] === 'increase')
                            ↑ {{ $report['comparison']['change_percent'] }}% {{ __('carbex.reports.pdf.increase') }}
                        @else
                            {{ __('carbex.reports.pdf.stable') }}
                        @endif
                    </strong>
                </p>
            @endif
        </div>

        {{-- Scope Breakdown --}}
        <div class="section">
            <h2 class="section-title">{{ __('carbex.reports.pdf.emissions_by_scope') }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>{{ __('carbex.reports.pdf.scope') }}</th>
                        <th>{{ __('carbex.reports.pdf.description') }}</th>
                        <th class="text-right">{{ __('carbex.reports.pdf.emissions') }}</th>
                        <th class="text-right">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['scope_breakdown'] as $scope)
                        <tr>
                            <td>
                                <span class="badge {{ match($scope['scope']) { 1 => 'badge-green', 2 => 'badge-blue', 3 => 'badge-purple', default => '' } }}">
                                    {{ $scope['label'] }}
                                </span>
                            </td>
                            <td>
                                @switch($scope['scope'])
                                    @case(1) {{ __('carbex.reports.pdf.direct_emissions') }} @break
                                    @case(2) {{ __('carbex.reports.pdf.indirect_energy_emissions') }} @break
                                    @case(3) {{ __('carbex.reports.pdf.value_chain_emissions') }} @break
                                @endswitch
                            </td>
                            <td class="text-right">{{ number_format($scope['value'], 2) }} t</td>
                            <td class="text-right">{{ $scope['percent'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Top Categories --}}
        <div class="section">
            <h2 class="section-title">{{ __('carbex.reports.pdf.top_categories') }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>{{ __('carbex.reports.pdf.category') }}</th>
                        <th>{{ __('carbex.reports.pdf.scope') }}</th>
                        <th class="text-right">{{ __('carbex.reports.pdf.emissions') }}</th>
                        <th class="text-right">%</th>
                        <th class="text-right">{{ __('carbex.reports.pdf.records') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_slice($report['category_breakdown'], 0, 10) as $category)
                        <tr>
                            <td>{{ $category['name'] }}</td>
                            <td>
                                <span class="badge {{ match($category['scope']) { 1 => 'badge-green', 2 => 'badge-blue', 3 => 'badge-purple', default => '' } }}">
                                    Scope {{ $category['scope'] }}
                                </span>
                            </td>
                            <td class="text-right">{{ number_format($category['emissions_tonnes'], 2) }} t</td>
                            <td class="text-right">{{ $category['percent'] }}%</td>
                            <td class="text-right">{{ $category['count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Sites Comparison --}}
        @if(isset($report['sites']) && count($report['sites']) > 0)
            <div class="section">
                <h2 class="section-title">{{ __('carbex.reports.pdf.emissions_by_site') }}</h2>
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('carbex.reports.pdf.site') }}</th>
                            <th>{{ __('carbex.reports.pdf.location') }}</th>
                            <th class="text-right">{{ __('carbex.reports.pdf.emissions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report['sites'] as $site)
                            <tr>
                                <td>{{ $site['name'] }}</td>
                                <td>{{ $site['city'] ?? '-' }}</td>
                                <td class="text-right">{{ number_format($site['value'], 2) }} t CO₂e</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Methodology --}}
        <div class="methodology-box">
            <h4>{{ __('carbex.reports.pdf.methodology') }}</h4>
            <p>
                <strong>{{ __('carbex.reports.pdf.standard') }}:</strong> {{ $report['methodology']['standard'] }}<br>
                <strong>{{ __('carbex.reports.pdf.emission_factors') }}:</strong> {{ $report['methodology']['emission_source']['name'] }} ({{ $report['methodology']['emission_source']['version'] }})<br>
                <strong>{{ __('carbex.reports.pdf.note') }}:</strong> {{ $report['methodology']['uncertainty'] }}
            </p>
        </div>

        {{-- Footer --}}
        <div class="footer">
            {{ __('carbex.reports.pdf.report_generated_on') }} {{ now()->format('d/m/Y H:i') }} |
            {{ config('app.name') }} - {{ config('app.url') }}
        </div>
    </div>
</body>
</html>
