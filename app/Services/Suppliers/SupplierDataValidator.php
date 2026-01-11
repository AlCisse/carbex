<?php

namespace App\Services\Suppliers;

class SupplierDataValidator
{
    /**
     * Validation rules for emission data.
     */
    protected array $rules = [
        'scope1_total' => [
            'min' => 0,
            'max' => 1000000000, // 1 billion tCO2e
        ],
        'scope2_location' => [
            'min' => 0,
            'max' => 1000000000,
        ],
        'scope2_market' => [
            'min' => 0,
            'max' => 1000000000,
        ],
        'scope3_total' => [
            'min' => 0,
            'max' => 10000000000, // 10 billion tCO2e
        ],
        'revenue' => [
            'min' => 0,
            'max' => 1000000000000, // 1 trillion
        ],
        'employees' => [
            'min' => 1,
            'max' => 10000000,
        ],
    ];

    /**
     * Validate emission data.
     */
    public function validate(array $data, array $requestedFields): array
    {
        $errors = [];
        $warnings = [];

        // Check required fields
        foreach ($requestedFields as $field) {
            if ($this->isRequired($field) && !$this->hasValue($data, $field)) {
                $errors[$field] = $this->getRequiredMessage($field);
            }
        }

        // Validate ranges
        foreach ($this->rules as $field => $rule) {
            if ($this->hasValue($data, $field)) {
                $value = $this->getValue($data, $field);

                if ($value < $rule['min']) {
                    $errors[$field] = "Value must be at least {$rule['min']}";
                }

                if ($value > $rule['max']) {
                    $errors[$field] = "Value exceeds maximum allowed ({$rule['max']})";
                }
            }
        }

        // Cross-field validations
        $crossValidation = $this->validateCrossFields($data);
        $errors = array_merge($errors, $crossValidation['errors']);
        $warnings = array_merge($warnings, $crossValidation['warnings']);

        // Data quality checks
        $qualityChecks = $this->checkDataQuality($data);
        $warnings = array_merge($warnings, $qualityChecks);

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Check if a field is required.
     */
    protected function isRequired(string $field): bool
    {
        // At minimum, we need either scope1 or scope2 data
        return in_array($field, ['scope1_total', 'scope2_location', 'scope2_market']);
    }

    /**
     * Check if data has a value for a field.
     */
    protected function hasValue(array $data, string $field): bool
    {
        $value = $this->getValue($data, $field);

        return $value !== null && $value !== '';
    }

    /**
     * Get value from nested data.
     */
    protected function getValue(array $data, string $field): mixed
    {
        $parts = explode('.', $field);
        $current = $data;

        foreach ($parts as $part) {
            if (!isset($current[$part])) {
                return null;
            }
            $current = $current[$part];
        }

        return $current;
    }

    /**
     * Get required field message.
     */
    protected function getRequiredMessage(string $field): string
    {
        $labels = [
            'scope1_total' => 'Scope 1 emissions',
            'scope2_location' => 'Scope 2 (location-based) emissions',
            'scope2_market' => 'Scope 2 (market-based) emissions',
            'revenue' => 'Annual revenue',
            'employees' => 'Number of employees',
        ];

        $label = $labels[$field] ?? $field;

        return "{$label} is required.";
    }

    /**
     * Validate cross-field relationships.
     */
    protected function validateCrossFields(array $data): array
    {
        $errors = [];
        $warnings = [];

        // Scope 1 breakdown should match total
        if ($this->hasValue($data, 'scope1_total') && isset($data['scope1_breakdown'])) {
            $breakdownSum = array_sum(array_filter($data['scope1_breakdown'], 'is_numeric'));
            $total = $data['scope1_total'];

            if ($breakdownSum > 0 && abs($breakdownSum - $total) / max($total, 1) > 0.05) {
                $warnings['scope1_breakdown'] = "Scope 1 breakdown ({$breakdownSum}) doesn't match total ({$total}).";
            }
        }

        // Scope 2 breakdown should match total
        $scope2Total = $data['scope2_market'] ?? $data['scope2_location'] ?? 0;
        if ($scope2Total > 0 && isset($data['scope2_breakdown'])) {
            $breakdownSum = array_sum(array_filter($data['scope2_breakdown'], 'is_numeric'));

            if ($breakdownSum > 0 && abs($breakdownSum - $scope2Total) / max($scope2Total, 1) > 0.05) {
                $warnings['scope2_breakdown'] = "Scope 2 breakdown ({$breakdownSum}) doesn't match total ({$scope2Total}).";
            }
        }

        // Market-based should typically be >= location-based for green energy claims
        if ($this->hasValue($data, 'scope2_location') && $this->hasValue($data, 'scope2_market')) {
            if ($data['scope2_market'] > $data['scope2_location'] * 1.5) {
                $warnings['scope2_market'] = 'Market-based emissions are significantly higher than location-based. Please verify.';
            }
        }

        // Revenue/employee sanity check
        if ($this->hasValue($data, 'revenue') && $this->hasValue($data, 'employees')) {
            $revenuePerEmployee = $data['revenue'] / $data['employees'];

            if ($revenuePerEmployee < 10000) {
                $warnings['revenue'] = 'Revenue per employee seems very low. Please verify figures.';
            }

            if ($revenuePerEmployee > 10000000) {
                $warnings['revenue'] = 'Revenue per employee seems very high. Please verify figures.';
            }
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Check data quality and provide warnings.
     */
    protected function checkDataQuality(array $data): array
    {
        $warnings = [];

        // Check emission intensity
        if ($this->hasValue($data, 'revenue') && $data['revenue'] > 0) {
            $totalEmissions = ($data['scope1_total'] ?? 0) + ($data['scope2_market'] ?? $data['scope2_location'] ?? 0);

            if ($totalEmissions > 0) {
                $intensity = $totalEmissions / $data['revenue'];

                // Typical intensity is 0.0001 to 1 kgCO2e/EUR
                if ($intensity > 1) {
                    $warnings['intensity'] = 'Emission intensity is very high. Please verify emission and revenue figures.';
                }

                if ($intensity < 0.00001) {
                    $warnings['intensity'] = 'Emission intensity is very low. Please verify emission and revenue figures.';
                }
            }
        }

        // Check for incomplete data
        $hasScope1 = $this->hasValue($data, 'scope1_total');
        $hasScope2 = $this->hasValue($data, 'scope2_location') || $this->hasValue($data, 'scope2_market');

        if (!$hasScope1 && !$hasScope2) {
            $warnings['completeness'] = 'No emission data provided. At least Scope 1 or Scope 2 data is recommended.';
        } elseif (!$hasScope1) {
            $warnings['scope1'] = 'Consider providing Scope 1 data for a complete emission profile.';
        } elseif (!$hasScope2) {
            $warnings['scope2'] = 'Consider providing Scope 2 data for a complete emission profile.';
        }

        // Verification data
        if ($this->hasValue($data, 'verification_standard') && !$this->hasValue($data, 'verifier_name')) {
            $warnings['verification'] = 'Verification standard provided but verifier name is missing.';
        }

        return $warnings;
    }

    /**
     * Calculate data completeness score.
     */
    public function getCompletenessScore(array $data, array $requestedFields): int
    {
        $filledFields = 0;
        $totalFields = count($requestedFields);

        foreach ($requestedFields as $field) {
            if ($this->hasValue($data, $field)) {
                $filledFields++;
            }
        }

        return $totalFields > 0 ? (int) round(($filledFields / $totalFields) * 100) : 0;
    }

    /**
     * Get validation summary.
     */
    public function getSummary(array $data): array
    {
        $scope1 = $data['scope1_total'] ?? 0;
        $scope2 = $data['scope2_market'] ?? $data['scope2_location'] ?? 0;
        $scope3 = $data['scope3_total'] ?? 0;
        $total = $scope1 + $scope2 + $scope3;

        $intensity = null;
        if (isset($data['revenue']) && $data['revenue'] > 0) {
            $intensity = ($scope1 + $scope2) / $data['revenue'];
        }

        return [
            'scope1_total' => $scope1,
            'scope2_total' => $scope2,
            'scope3_total' => $scope3,
            'total_emissions' => $total,
            'emission_intensity' => $intensity,
            'has_verification' => !empty($data['verification_standard']),
            'has_breakdown' => !empty($data['scope1_breakdown']) || !empty($data['scope2_breakdown']),
        ];
    }
}
