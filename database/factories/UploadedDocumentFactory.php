<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Organization;
use App\Models\UploadedDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UploadedDocument>
 */
class UploadedDocumentFactory extends Factory
{
    protected $model = UploadedDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'assessment_id' => null,
            'uploaded_by' => User::factory(),
            'original_filename' => $this->faker->word() . '.pdf',
            'storage_path' => 'documents/' . $this->faker->uuid() . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(10000, 5000000),
            'file_hash' => $this->faker->sha256(),
            'document_type' => $this->faker->randomElement([
                UploadedDocument::TYPE_INVOICE,
                UploadedDocument::TYPE_ENERGY_BILL,
                UploadedDocument::TYPE_FUEL_RECEIPT,
            ]),
            'processing_status' => UploadedDocument::STATUS_PENDING,
            'extracted_data' => null,
            'extraction_metadata' => null,
            'ai_confidence' => null,
            'ai_model_used' => null,
            'processing_time_ms' => null,
            'is_validated' => false,
            'validated_by' => null,
            'validated_at' => null,
            'validation_corrections' => null,
            'emission_record_id' => null,
            'emission_created' => false,
            'error_message' => null,
            'retry_count' => 0,
        ];
    }

    /**
     * Set the organization.
     */
    public function forOrganization(Organization $organization): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organization->id,
        ]);
    }

    /**
     * Set the assessment.
     */
    public function forAssessment(Assessment $assessment): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_id' => $assessment->id,
            'organization_id' => $assessment->organization_id,
        ]);
    }

    /**
     * Set as pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'processing_status' => UploadedDocument::STATUS_PENDING,
        ]);
    }

    /**
     * Set as processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'processing_status' => UploadedDocument::STATUS_PROCESSING,
        ]);
    }

    /**
     * Set as completed with extracted data.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'processing_status' => UploadedDocument::STATUS_COMPLETED,
            'ai_confidence' => $this->faker->randomFloat(2, 0.7, 1.0),
            'processing_time_ms' => $this->faker->numberBetween(500, 5000),
            'extracted_data' => [
                'document_type' => 'invoice',
                'supplier_name' => $this->faker->company(),
                'date' => $this->faker->date(),
                'total_amount' => $this->faker->randomFloat(2, 100, 10000),
                'line_items' => [
                    [
                        'description' => 'Électricité',
                        'quantity' => $this->faker->numberBetween(100, 1000),
                        'unit' => 'kWh',
                        'amount' => $this->faker->randomFloat(2, 50, 500),
                    ],
                ],
            ],
        ]);
    }

    /**
     * Set as failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'processing_status' => UploadedDocument::STATUS_FAILED,
            'error_message' => 'Extraction failed: ' . $this->faker->sentence(),
            'retry_count' => $this->faker->numberBetween(1, 3),
        ]);
    }

    /**
     * Set as needs review.
     */
    public function needsReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'processing_status' => UploadedDocument::STATUS_NEEDS_REVIEW,
            'ai_confidence' => $this->faker->randomFloat(2, 0.3, 0.69),
            'extracted_data' => [
                'document_type' => 'other',
                'supplier_name' => null,
                'date' => $this->faker->date(),
                'total_amount' => $this->faker->randomFloat(2, 100, 10000),
            ],
        ]);
    }

    /**
     * Set as validated.
     */
    public function validated(): static
    {
        return $this->completed()->state(fn (array $attributes) => [
            'is_validated' => true,
            'validated_by' => User::factory(),
            'validated_at' => now(),
        ]);
    }

    /**
     * Set with emission created.
     */
    public function withEmission(): static
    {
        return $this->validated()->state(fn (array $attributes) => [
            'emission_created' => true,
        ]);
    }

    /**
     * Set as invoice.
     */
    public function invoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => UploadedDocument::TYPE_INVOICE,
        ]);
    }

    /**
     * Set as energy bill.
     */
    public function energyBill(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => UploadedDocument::TYPE_ENERGY_BILL,
        ]);
    }

    /**
     * Set as fuel receipt.
     */
    public function fuelReceipt(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => UploadedDocument::TYPE_FUEL_RECEIPT,
        ]);
    }

    /**
     * Set as image file.
     */
    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'original_filename' => $this->faker->word() . '.jpg',
            'storage_path' => 'documents/' . $this->faker->uuid() . '.jpg',
            'mime_type' => 'image/jpeg',
        ]);
    }

    /**
     * Set as Excel file.
     */
    public function excel(): static
    {
        return $this->state(fn (array $attributes) => [
            'original_filename' => $this->faker->word() . '.xlsx',
            'storage_path' => 'documents/' . $this->faker->uuid() . '.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
