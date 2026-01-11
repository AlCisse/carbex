{{-- Methodology Partial for PDF Reports --}}
<div class="methodology-section">
    <h2 class="section-title">{{ __('carbex.methodology.title') }}</h2>

    {{-- Calculation Standards --}}
    <div class="subsection">
        <h3>{{ __('carbex.methodology.calculation_standards') }}</h3>
        <table>
            <tbody>
                <tr>
                    <td><strong>{{ __('carbex.methodology.primary_standard') }}</strong></td>
                    <td>{{ $methodology['standard'] ?? 'GHG Protocol Corporate Standard' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('carbex.methodology.consolidation_approach') }}</strong></td>
                    <td>{{ $methodology['consolidation_approach'] ?? __('carbex.methodology.operational_control') }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('carbex.methodology.base_year') }}</strong></td>
                    <td>{{ $methodology['base_year'] ?? date('Y') }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('carbex.methodology.reporting_period') }}</strong></td>
                    <td>{{ $methodology['period_start'] ?? '' }} - {{ $methodology['period_end'] ?? '' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Emission Factor Sources --}}
    <div class="subsection">
        <h3>{{ __('carbex.methodology.emission_factor_sources') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ __('carbex.methodology.source') }}</th>
                    <th>{{ __('carbex.methodology.version') }}</th>
                    <th>{{ __('carbex.methodology.applied_to') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($methodology['emission_sources'] ?? [] as $source)
                    <tr>
                        <td>{{ $source['name'] }}</td>
                        <td>{{ $source['version'] }}</td>
                        <td>{{ $source['applied_to'] }}</td>
                    </tr>
                @endforeach
                @if(empty($methodology['emission_sources']))
                    <tr>
                        <td>ADEME Base Carbone</td>
                        <td>2024</td>
                        <td>{{ __('carbex.methodology.all_french_operations') }}</td>
                    </tr>
                    <tr>
                        <td>DEFRA UK GHG Conversion Factors</td>
                        <td>2024</td>
                        <td>{{ __('carbex.methodology.uk_operations') }}</td>
                    </tr>
                    <tr>
                        <td>IEA Emission Factors</td>
                        <td>2023</td>
                        <td>{{ __('carbex.methodology.other_countries') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Scope Definitions --}}
    <div class="subsection">
        <h3>{{ __('carbex.methodology.scope_definitions') }}</h3>
        <div class="scope-definitions">
            <div class="scope-definition">
                <h4><span class="badge badge-green">Scope 1</span> {{ __('carbex.methodology.direct_emissions') }}</h4>
                <p>{{ __('carbex.methodology.scope1_desc') }}</p>
                <ul>
                    <li>{{ __('carbex.methodology.company_vehicles') }}</li>
                    <li>{{ __('carbex.methodology.onsite_fuel') }}</li>
                    <li>{{ __('carbex.methodology.fugitive_emissions') }}</li>
                    <li>{{ __('carbex.methodology.process_emissions') }}</li>
                </ul>
            </div>

            <div class="scope-definition">
                <h4><span class="badge badge-blue">Scope 2</span> {{ __('carbex.methodology.energy_indirect') }}</h4>
                <p>{{ __('carbex.methodology.scope2_desc') }}</p>
                <ul>
                    <li>{{ __('carbex.methodology.purchased_electricity') }}</li>
                    <li>{{ __('carbex.methodology.district_heating') }}</li>
                    <li>{{ __('carbex.methodology.steam') }}</li>
                </ul>
                <p><em>{{ __('carbex.methodology.location_based_note') }}</em></p>
            </div>

            <div class="scope-definition">
                <h4><span class="badge badge-purple">Scope 3</span> {{ __('carbex.methodology.value_chain') }}</h4>
                <p>{{ __('carbex.methodology.scope3_desc') }}</p>
                <ul>
                    @if(isset($methodology['scope_3_categories']))
                        @foreach($methodology['scope_3_categories'] as $category)
                            <li>{{ $category }}</li>
                        @endforeach
                    @else
                        <li>{{ __('carbex.methodology.cat1_purchased') }}</li>
                        <li>{{ __('carbex.methodology.cat5_waste') }}</li>
                        <li>{{ __('carbex.methodology.cat6_travel') }}</li>
                        <li>{{ __('carbex.methodology.cat7_commuting') }}</li>
                        <li>{{ __('carbex.methodology.cat8_leased') }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    {{-- Data Quality --}}
    <div class="subsection">
        <h3>{{ __('carbex.methodology.data_quality_assessment') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ __('carbex.methodology.data_type') }}</th>
                    <th>{{ __('carbex.methodology.source') }}</th>
                    <th>{{ __('carbex.methodology.quality_score') }}</th>
                    <th>{{ __('carbex.methodology.coverage') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($methodology['data_quality'] ?? [] as $item)
                    <tr>
                        <td>{{ $item['type'] }}</td>
                        <td>{{ $item['source'] }}</td>
                        <td>
                            <span class="quality-badge quality-{{ $item['score'] >= 80 ? 'high' : ($item['score'] >= 50 ? 'medium' : 'low') }}">
                                {{ $item['score'] }}%
                            </span>
                        </td>
                        <td>{{ $item['coverage'] }}</td>
                    </tr>
                @endforeach
                @if(empty($methodology['data_quality']))
                    <tr>
                        <td>{{ __('carbex.methodology.energy_consumption') }}</td>
                        <td>{{ __('carbex.methodology.invoices_meters') }}</td>
                        <td><span class="quality-badge quality-high">95%</span></td>
                        <td>100%</td>
                    </tr>
                    <tr>
                        <td>{{ __('carbex.methodology.business_travel') }}</td>
                        <td>{{ __('carbex.methodology.bank_transactions') }}</td>
                        <td><span class="quality-badge quality-high">85%</span></td>
                        <td>90%</td>
                    </tr>
                    <tr>
                        <td>{{ __('carbex.methodology.purchased_goods') }}</td>
                        <td>{{ __('carbex.methodology.spend_based') }}</td>
                        <td><span class="quality-badge quality-medium">60%</span></td>
                        <td>75%</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Uncertainty & Limitations --}}
    <div class="subsection">
        <h3>{{ __('carbex.methodology.uncertainty_limitations') }}</h3>
        <div class="methodology-box">
            <p>{{ __('carbex.methodology.uncertainty_factors') }}</p>
            <ul>
                <li>{{ __('carbex.methodology.uncertainty_1') }}</li>
                <li>{{ __('carbex.methodology.uncertainty_2') }}</li>
                <li>{{ __('carbex.methodology.uncertainty_3') }}</li>
            </ul>
            <p><strong>{{ __('carbex.methodology.estimated_uncertainty') }}:</strong> ± {{ $methodology['uncertainty_percent'] ?? 15 }}%</p>
        </div>
    </div>

    {{-- Exclusions --}}
    @if(!empty($methodology['exclusions']))
        <div class="subsection">
            <h3>{{ __('carbex.methodology.exclusions') }}</h3>
            <p>{{ __('carbex.methodology.exclusions_desc') }}</p>
            <ul>
                @foreach($methodology['exclusions'] as $exclusion)
                    <li>
                        <strong>{{ $exclusion['item'] }}:</strong>
                        {{ $exclusion['reason'] }}
                        ({{ __('carbex.methodology.estimated_impact') }}: {{ $exclusion['impact'] ?? '< 1%' }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Verification Statement --}}
    <div class="verification-box">
        <h4>{{ __('carbex.methodology.verification_statement') }}</h4>
        @if(isset($methodology['verification']))
            <p>
                {{ __('carbex.methodology.verified_by') }}
                <strong>{{ $methodology['verification']['verifier'] }}</strong>
                {{ __('carbex.methodology.to_standard') }}
                <strong>{{ $methodology['verification']['standard'] }}</strong>.
            </p>
            <p>{{ __('carbex.methodology.verification_date') }}: {{ $methodology['verification']['date'] }}</p>
        @else
            <p>
                {{ __('carbex.methodology.ghg_prepared') }}
                {{ __('carbex.methodology.verification_recommended') }}
            </p>
        @endif
    </div>
</div>

<style>
    .methodology-section {
        margin-bottom: 30px;
    }
    .subsection {
        margin-bottom: 20px;
    }
    .subsection h3 {
        font-size: 12pt;
        color: #374151;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px solid #e5e7eb;
    }
    .scope-definitions {
        display: block;
    }
    .scope-definition {
        margin-bottom: 15px;
        padding: 10px;
        background: #f9fafb;
        border-radius: 5px;
    }
    .scope-definition h4 {
        margin-bottom: 5px;
    }
    .scope-definition ul {
        margin-left: 20px;
        margin-top: 5px;
    }
    .scope-definition li {
        font-size: 9pt;
        color: #4b5563;
    }
    .quality-badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 8pt;
        font-weight: 600;
    }
    .quality-high { background: #d1fae5; color: #065f46; }
    .quality-medium { background: #fef3c7; color: #92400e; }
    .quality-low { background: #fee2e2; color: #991b1b; }
    .methodology-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: 15px;
        border-radius: 5px;
    }
    .methodology-box ul {
        margin-left: 20px;
        margin-top: 10px;
    }
    .verification-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 15px;
        border-radius: 5px;
        margin-top: 20px;
    }
    .verification-box h4 {
        color: #166534;
        margin-bottom: 10px;
    }
</style>
