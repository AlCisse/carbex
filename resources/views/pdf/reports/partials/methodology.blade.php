{{-- Methodology Partial for PDF Reports --}}
<div class="methodology-section">
    <h2 class="section-title">{{ __('linscarbon.methodology.title') }}</h2>

    {{-- Calculation Standards --}}
    <div class="subsection">
        <h3>{{ __('linscarbon.methodology.calculation_standards') }}</h3>
        <table>
            <tbody>
                <tr>
                    <td><strong>{{ __('linscarbon.methodology.primary_standard') }}</strong></td>
                    <td>{{ $methodology['standard'] ?? 'GHG Protocol Corporate Standard' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('linscarbon.methodology.consolidation_approach') }}</strong></td>
                    <td>{{ $methodology['consolidation_approach'] ?? __('linscarbon.methodology.operational_control') }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('linscarbon.methodology.base_year') }}</strong></td>
                    <td>{{ $methodology['base_year'] ?? date('Y') }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('linscarbon.methodology.reporting_period') }}</strong></td>
                    <td>{{ $methodology['period_start'] ?? '' }} - {{ $methodology['period_end'] ?? '' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Emission Factor Sources --}}
    <div class="subsection">
        <h3>{{ __('linscarbon.methodology.emission_factor_sources') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ __('linscarbon.methodology.source') }}</th>
                    <th>{{ __('linscarbon.methodology.version') }}</th>
                    <th>{{ __('linscarbon.methodology.applied_to') }}</th>
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
                        <td>{{ __('linscarbon.methodology.all_french_operations') }}</td>
                    </tr>
                    <tr>
                        <td>DEFRA UK GHG Conversion Factors</td>
                        <td>2024</td>
                        <td>{{ __('linscarbon.methodology.uk_operations') }}</td>
                    </tr>
                    <tr>
                        <td>IEA Emission Factors</td>
                        <td>2023</td>
                        <td>{{ __('linscarbon.methodology.other_countries') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Scope Definitions --}}
    <div class="subsection">
        <h3>{{ __('linscarbon.methodology.scope_definitions') }}</h3>
        <div class="scope-definitions">
            <div class="scope-definition">
                <h4><span class="badge badge-green">Scope 1</span> {{ __('linscarbon.methodology.direct_emissions') }}</h4>
                <p>{{ __('linscarbon.methodology.scope1_desc') }}</p>
                <ul>
                    <li>{{ __('linscarbon.methodology.company_vehicles') }}</li>
                    <li>{{ __('linscarbon.methodology.onsite_fuel') }}</li>
                    <li>{{ __('linscarbon.methodology.fugitive_emissions') }}</li>
                    <li>{{ __('linscarbon.methodology.process_emissions') }}</li>
                </ul>
            </div>

            <div class="scope-definition">
                <h4><span class="badge badge-blue">Scope 2</span> {{ __('linscarbon.methodology.energy_indirect') }}</h4>
                <p>{{ __('linscarbon.methodology.scope2_desc') }}</p>
                <ul>
                    <li>{{ __('linscarbon.methodology.purchased_electricity') }}</li>
                    <li>{{ __('linscarbon.methodology.district_heating') }}</li>
                    <li>{{ __('linscarbon.methodology.steam') }}</li>
                </ul>
                <p><em>{{ __('linscarbon.methodology.location_based_note') }}</em></p>
            </div>

            <div class="scope-definition">
                <h4><span class="badge badge-purple">Scope 3</span> {{ __('linscarbon.methodology.value_chain') }}</h4>
                <p>{{ __('linscarbon.methodology.scope3_desc') }}</p>
                <ul>
                    @if(isset($methodology['scope_3_categories']))
                        @foreach($methodology['scope_3_categories'] as $category)
                            <li>{{ $category }}</li>
                        @endforeach
                    @else
                        <li>{{ __('linscarbon.methodology.cat1_purchased') }}</li>
                        <li>{{ __('linscarbon.methodology.cat5_waste') }}</li>
                        <li>{{ __('linscarbon.methodology.cat6_travel') }}</li>
                        <li>{{ __('linscarbon.methodology.cat7_commuting') }}</li>
                        <li>{{ __('linscarbon.methodology.cat8_leased') }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    {{-- Data Quality --}}
    <div class="subsection">
        <h3>{{ __('linscarbon.methodology.data_quality_assessment') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ __('linscarbon.methodology.data_type') }}</th>
                    <th>{{ __('linscarbon.methodology.source') }}</th>
                    <th>{{ __('linscarbon.methodology.quality_score') }}</th>
                    <th>{{ __('linscarbon.methodology.coverage') }}</th>
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
                        <td>{{ __('linscarbon.methodology.energy_consumption') }}</td>
                        <td>{{ __('linscarbon.methodology.invoices_meters') }}</td>
                        <td><span class="quality-badge quality-high">95%</span></td>
                        <td>100%</td>
                    </tr>
                    <tr>
                        <td>{{ __('linscarbon.methodology.business_travel') }}</td>
                        <td>{{ __('linscarbon.methodology.bank_transactions') }}</td>
                        <td><span class="quality-badge quality-high">85%</span></td>
                        <td>90%</td>
                    </tr>
                    <tr>
                        <td>{{ __('linscarbon.methodology.purchased_goods') }}</td>
                        <td>{{ __('linscarbon.methodology.spend_based') }}</td>
                        <td><span class="quality-badge quality-medium">60%</span></td>
                        <td>75%</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Uncertainty & Limitations --}}
    <div class="subsection">
        <h3>{{ __('linscarbon.methodology.uncertainty_limitations') }}</h3>
        <div class="methodology-box">
            <p>{{ __('linscarbon.methodology.uncertainty_factors') }}</p>
            <ul>
                <li>{{ __('linscarbon.methodology.uncertainty_1') }}</li>
                <li>{{ __('linscarbon.methodology.uncertainty_2') }}</li>
                <li>{{ __('linscarbon.methodology.uncertainty_3') }}</li>
            </ul>
            <p><strong>{{ __('linscarbon.methodology.estimated_uncertainty') }}:</strong> ± {{ $methodology['uncertainty_percent'] ?? 15 }}%</p>
        </div>
    </div>

    {{-- Exclusions --}}
    @if(!empty($methodology['exclusions']))
        <div class="subsection">
            <h3>{{ __('linscarbon.methodology.exclusions') }}</h3>
            <p>{{ __('linscarbon.methodology.exclusions_desc') }}</p>
            <ul>
                @foreach($methodology['exclusions'] as $exclusion)
                    <li>
                        <strong>{{ $exclusion['item'] }}:</strong>
                        {{ $exclusion['reason'] }}
                        ({{ __('linscarbon.methodology.estimated_impact') }}: {{ $exclusion['impact'] ?? '< 1%' }})
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Verification Statement --}}
    <div class="verification-box">
        <h4>{{ __('linscarbon.methodology.verification_statement') }}</h4>
        @if(isset($methodology['verification']))
            <p>
                {{ __('linscarbon.methodology.verified_by') }}
                <strong>{{ $methodology['verification']['verifier'] }}</strong>
                {{ __('linscarbon.methodology.to_standard') }}
                <strong>{{ $methodology['verification']['standard'] }}</strong>.
            </p>
            <p>{{ __('linscarbon.methodology.verification_date') }}: {{ $methodology['verification']['date'] }}</p>
        @else
            <p>
                {{ __('linscarbon.methodology.ghg_prepared') }}
                {{ __('linscarbon.methodology.verification_recommended') }}
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
