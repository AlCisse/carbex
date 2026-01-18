{{-- Scope Breakdown Partial for PDF Reports --}}
<div class="scope-breakdown-section">
    <h2 class="section-title">{{ __('linscarbon.scope_breakdown.title') }}</h2>

    {{-- Overview Chart (Visual representation) --}}
    <div class="scope-overview">
        <div class="scope-bar">
            @php
                $total = ($scopes['scope_1']['tonnes'] ?? 0) + ($scopes['scope_2']['tonnes'] ?? 0) + ($scopes['scope_3']['tonnes'] ?? 0);
                $scope1Pct = $total > 0 ? (($scopes['scope_1']['tonnes'] ?? 0) / $total) * 100 : 0;
                $scope2Pct = $total > 0 ? (($scopes['scope_2']['tonnes'] ?? 0) / $total) * 100 : 0;
                $scope3Pct = $total > 0 ? (($scopes['scope_3']['tonnes'] ?? 0) / $total) * 100 : 0;
            @endphp
            <div class="bar-segment scope-1-bg" style="width: {{ $scope1Pct }}%;">
                @if($scope1Pct > 10) {{ round($scope1Pct) }}% @endif
            </div>
            <div class="bar-segment scope-2-bg" style="width: {{ $scope2Pct }}%;">
                @if($scope2Pct > 10) {{ round($scope2Pct) }}% @endif
            </div>
            <div class="bar-segment scope-3-bg" style="width: {{ $scope3Pct }}%;">
                @if($scope3Pct > 10) {{ round($scope3Pct) }}% @endif
            </div>
        </div>
        <div class="scope-legend">
            <span class="legend-item"><span class="legend-color scope-1-bg"></span> Scope 1</span>
            <span class="legend-item"><span class="legend-color scope-2-bg"></span> Scope 2</span>
            <span class="legend-item"><span class="legend-color scope-3-bg"></span> Scope 3</span>
        </div>
    </div>

    {{-- Scope 1: Direct Emissions --}}
    <div class="scope-detail">
        <div class="scope-header scope-1">
            <h3>
                <span class="badge badge-green">Scope 1</span>
                {{ __('linscarbon.scope_breakdown.direct_emissions') }}
            </h3>
            <div class="scope-total">
                <span class="value">{{ number_format($scopes['scope_1']['tonnes'] ?? 0, 2) }}</span>
                <span class="unit">t CO₂e</span>
            </div>
        </div>
        <p class="scope-description">
            {{ __('linscarbon.scope_breakdown.scope1_desc') }}
        </p>

        @if(!empty($scopes['scope_1']['categories']))
            <table>
                <thead>
                    <tr>
                        <th>{{ __('linscarbon.scope_breakdown.category') }}</th>
                        <th class="text-right">{{ __('linscarbon.scope_breakdown.emissions_tco2e') }}</th>
                        <th class="text-right">{{ __('linscarbon.scope_breakdown.share') }}</th>
                        @if(isset($scopes['scope_1']['categories'][0]['trend']))
                            <th class="text-center">{{ __('linscarbon.scope_breakdown.trend') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($scopes['scope_1']['categories'] as $category)
                        <tr>
                            <td>{{ $category['name'] }}</td>
                            <td class="text-right">{{ number_format($category['tonnes'], 2) }}</td>
                            <td class="text-right">{{ $category['percent'] }}%</td>
                            @if(isset($category['trend']))
                                <td class="text-center {{ $category['trend'] < 0 ? 'trend-down' : 'trend-up' }}">
                                    {{ $category['trend'] > 0 ? '+' : '' }}{{ $category['trend'] }}%
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="no-data">{{ __('linscarbon.scope_breakdown.no_scope1_data') }}</p>
        @endif
    </div>

    {{-- Scope 2: Energy Indirect Emissions --}}
    <div class="scope-detail">
        <div class="scope-header scope-2">
            <h3>
                <span class="badge badge-blue">Scope 2</span>
                {{ __('linscarbon.scope_breakdown.energy_indirect') }}
            </h3>
            <div class="scope-total">
                <span class="value">{{ number_format($scopes['scope_2']['tonnes'] ?? 0, 2) }}</span>
                <span class="unit">t CO₂e</span>
            </div>
        </div>
        <p class="scope-description">
            {{ __('linscarbon.scope_breakdown.scope2_desc') }}
        </p>

        @if(isset($scopes['scope_2']['method']))
            <p class="method-note">
                <strong>{{ __('linscarbon.scope_breakdown.calculation_method') }}:</strong>
                {{ $scopes['scope_2']['method'] === 'location' ? __('linscarbon.scope_breakdown.location_based') : __('linscarbon.scope_breakdown.market_based') }}
            </p>
        @endif

        @if(!empty($scopes['scope_2']['categories']))
            <table>
                <thead>
                    <tr>
                        <th>{{ __('linscarbon.scope_breakdown.category') }}</th>
                        <th class="text-right">{{ __('linscarbon.scope_breakdown.consumption') }}</th>
                        <th class="text-right">{{ __('linscarbon.scope_breakdown.emissions_tco2e') }}</th>
                        <th class="text-right">{{ __('linscarbon.scope_breakdown.share') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scopes['scope_2']['categories'] as $category)
                        <tr>
                            <td>{{ $category['name'] }}</td>
                            <td class="text-right">
                                @if(isset($category['consumption']))
                                    {{ number_format($category['consumption'], 0) }} {{ $category['unit'] ?? 'kWh' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($category['tonnes'], 2) }}</td>
                            <td class="text-right">{{ $category['percent'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="no-data">{{ __('linscarbon.scope_breakdown.no_scope2_data') }}</p>
        @endif

        {{-- Electricity mix information --}}
        @if(isset($scopes['scope_2']['electricity_mix']))
            <div class="electricity-mix">
                <h4>{{ __('linscarbon.scope_breakdown.electricity_mix') }}</h4>
                <p>
                    {{ __('linscarbon.scope_breakdown.grid_emission_factor') }}:
                    <strong>{{ $scopes['scope_2']['electricity_mix']['factor'] }} kg CO₂e/kWh</strong>
                    ({{ $scopes['scope_2']['electricity_mix']['source'] }})
                </p>
            </div>
        @endif
    </div>

    {{-- Scope 3: Value Chain Emissions --}}
    <div class="scope-detail">
        <div class="scope-header scope-3">
            <h3>
                <span class="badge badge-purple">Scope 3</span>
                {{ __('linscarbon.scope_breakdown.value_chain') }}
            </h3>
            <div class="scope-total">
                <span class="value">{{ number_format($scopes['scope_3']['tonnes'] ?? 0, 2) }}</span>
                <span class="unit">t CO₂e</span>
            </div>
        </div>
        <p class="scope-description">
            {{ __('linscarbon.scope_breakdown.scope3_desc') }}
        </p>

        @if(!empty($scopes['scope_3']['categories']))
            <table>
                <thead>
                    <tr>
                        <th>{{ __('linscarbon.scope_breakdown.category') }}</th>
                        <th>{{ __('linscarbon.scope_breakdown.ghg_category') }}</th>
                        <th class="text-right">{{ __('linscarbon.scope_breakdown.emissions_tco2e') }}</th>
                        <th class="text-right">{{ __('linscarbon.scope_breakdown.share') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scopes['scope_3']['categories'] as $category)
                        <tr>
                            <td>{{ $category['name'] }}</td>
                            <td>
                                @if(isset($category['ghg_category']))
                                    Cat. {{ $category['ghg_category'] }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($category['tonnes'], 2) }}</td>
                            <td class="text-right">{{ $category['percent'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="no-data">{{ __('linscarbon.scope_breakdown.no_scope3_data') }}</p>
        @endif

        {{-- Scope 3 Coverage --}}
        @if(isset($scopes['scope_3']['coverage']))
            <div class="coverage-note">
                <h4>{{ __('linscarbon.scope_breakdown.scope3_coverage') }}</h4>
                <p>
                    {{ __('linscarbon.scope_breakdown.currently_tracking') }}
                    <strong>{{ count($scopes['scope_3']['coverage']['included'] ?? []) }}</strong>
                    {{ __('linscarbon.scope_breakdown.of_15_categories') }}.
                </p>
                @if(!empty($scopes['scope_3']['coverage']['not_relevant']))
                    <p class="small">
                        {{ __('linscarbon.scope_breakdown.not_relevant') }}:
                        {{ implode(', ', $scopes['scope_3']['coverage']['not_relevant']) }}
                    </p>
                @endif
            </div>
        @endif
    </div>

    {{-- Summary Table --}}
    <div class="summary-table">
        <h3>{{ __('linscarbon.scope_breakdown.summary_by_scope') }}</h3>
        <table class="totals-table">
            <thead>
                <tr>
                    <th>{{ __('linscarbon.scope_breakdown.scope') }}</th>
                    <th class="text-right">{{ __('linscarbon.scope_breakdown.emissions_tco2e') }}</th>
                    <th class="text-right">{{ __('linscarbon.scope_breakdown.share_of_total') }}</th>
                    @if(isset($scopes['scope_1']['previous_period']))
                        <th class="text-right">{{ __('linscarbon.scope_breakdown.vs_previous') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-green">Scope 1</span></td>
                    <td class="text-right">{{ number_format($scopes['scope_1']['tonnes'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ round($scope1Pct, 1) }}%</td>
                    @if(isset($scopes['scope_1']['previous_period']))
                        <td class="text-right {{ $scopes['scope_1']['change'] < 0 ? 'trend-down' : 'trend-up' }}">
                            {{ $scopes['scope_1']['change'] > 0 ? '+' : '' }}{{ $scopes['scope_1']['change'] }}%
                        </td>
                    @endif
                </tr>
                <tr>
                    <td><span class="badge badge-blue">Scope 2</span></td>
                    <td class="text-right">{{ number_format($scopes['scope_2']['tonnes'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ round($scope2Pct, 1) }}%</td>
                    @if(isset($scopes['scope_2']['previous_period']))
                        <td class="text-right {{ $scopes['scope_2']['change'] < 0 ? 'trend-down' : 'trend-up' }}">
                            {{ $scopes['scope_2']['change'] > 0 ? '+' : '' }}{{ $scopes['scope_2']['change'] }}%
                        </td>
                    @endif
                </tr>
                <tr>
                    <td><span class="badge badge-purple">Scope 3</span></td>
                    <td class="text-right">{{ number_format($scopes['scope_3']['tonnes'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ round($scope3Pct, 1) }}%</td>
                    @if(isset($scopes['scope_3']['previous_period']))
                        <td class="text-right {{ $scopes['scope_3']['change'] < 0 ? 'trend-down' : 'trend-up' }}">
                            {{ $scopes['scope_3']['change'] > 0 ? '+' : '' }}{{ $scopes['scope_3']['change'] }}%
                        </td>
                    @endif
                </tr>
                <tr class="totals-row">
                    <td><strong>{{ __('linscarbon.scope_breakdown.total') }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($total, 2) }}</strong></td>
                    <td class="text-right"><strong>100%</strong></td>
                    @if(isset($scopes['total_change']))
                        <td class="text-right {{ $scopes['total_change'] < 0 ? 'trend-down' : 'trend-up' }}">
                            <strong>{{ $scopes['total_change'] > 0 ? '+' : '' }}{{ $scopes['total_change'] }}%</strong>
                        </td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
    .scope-breakdown-section {
        margin-bottom: 30px;
    }
    .scope-overview {
        margin-bottom: 25px;
    }
    .scope-bar {
        display: flex;
        height: 30px;
        border-radius: 5px;
        overflow: hidden;
        margin-bottom: 10px;
    }
    .bar-segment {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 10pt;
        font-weight: 600;
    }
    .scope-1-bg { background: #10b981; }
    .scope-2-bg { background: #3b82f6; }
    .scope-3-bg { background: #8b5cf6; }
    .scope-legend {
        display: flex;
        justify-content: center;
        gap: 20px;
    }
    .legend-item {
        display: flex;
        align-items: center;
        font-size: 9pt;
    }
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        margin-right: 5px;
    }
    .scope-detail {
        margin-bottom: 25px;
        padding: 15px;
        background: #f9fafb;
        border-radius: 5px;
    }
    .scope-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 2px solid;
    }
    .scope-header.scope-1 { border-color: #10b981; }
    .scope-header.scope-2 { border-color: #3b82f6; }
    .scope-header.scope-3 { border-color: #8b5cf6; }
    .scope-header h3 {
        font-size: 12pt;
        margin: 0;
    }
    .scope-total .value {
        font-size: 18pt;
        font-weight: bold;
    }
    .scope-total .unit {
        font-size: 10pt;
        color: #6b7280;
    }
    .scope-description {
        font-size: 9pt;
        color: #6b7280;
        margin-bottom: 15px;
    }
    .method-note {
        font-size: 9pt;
        color: #374151;
        margin-bottom: 10px;
        padding: 5px 10px;
        background: #e5e7eb;
        border-radius: 3px;
    }
    .no-data {
        font-style: italic;
        color: #9ca3af;
        text-align: center;
        padding: 20px;
    }
    .electricity-mix, .coverage-note {
        margin-top: 15px;
        padding: 10px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 5px;
    }
    .electricity-mix h4, .coverage-note h4 {
        font-size: 10pt;
        margin-bottom: 5px;
    }
    .small {
        font-size: 8pt;
        color: #6b7280;
    }
    .summary-table {
        margin-top: 25px;
    }
    .summary-table h3 {
        font-size: 12pt;
        margin-bottom: 10px;
    }
    .totals-table {
        background: white;
    }
    .totals-row {
        background: #f3f4f6;
    }
    .trend-up { color: #dc2626; }
    .trend-down { color: #16a34a; }
</style>
