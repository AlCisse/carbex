<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uploaded_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('assessment_id')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            // File information
            $table->string('original_filename');
            $table->string('storage_path');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->string('file_hash', 64)->nullable(); // SHA-256 for deduplication

            // Document classification
            $table->enum('document_type', [
                'invoice',           // Facture
                'energy_bill',       // Facture énergie
                'fuel_receipt',      // Ticket carburant
                'transport_invoice', // Facture transport
                'purchase_order',    // Bon de commande
                'bank_statement',    // Relevé bancaire
                'expense_report',    // Note de frais
                'other',
            ])->default('other');

            // Processing status
            $table->enum('processing_status', [
                'pending',           // En attente
                'processing',        // En cours de traitement
                'completed',         // Terminé
                'failed',            // Échec
                'needs_review',      // Nécessite révision
            ])->default('pending');

            // AI extraction results
            $table->json('extracted_data')->nullable();      // Données extraites
            $table->json('extraction_metadata')->nullable(); // Métadonnées extraction
            $table->decimal('ai_confidence', 3, 2)->nullable(); // Score confiance 0.00-1.00
            $table->text('ai_model_used')->nullable();       // Modèle IA utilisé
            $table->unsignedInteger('processing_time_ms')->nullable(); // Temps de traitement

            // Validation
            $table->boolean('is_validated')->default(false);
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->json('validation_corrections')->nullable(); // Corrections utilisateur

            // Emission mapping
            $table->uuid('emission_record_id')->nullable();  // Lien vers EmissionRecord créé
            $table->boolean('emission_created')->default(false);

            // Error handling
            $table->text('error_message')->nullable();
            $table->unsignedTinyInteger('retry_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('organization_id')
                ->references('id')->on('organizations')
                ->onDelete('cascade');
            $table->foreign('assessment_id')
                ->references('id')->on('assessments')
                ->onDelete('set null');
            $table->foreign('emission_record_id')
                ->references('id')->on('emission_records')
                ->onDelete('set null');

            // Indexes
            $table->index(['organization_id', 'processing_status']);
            $table->index(['organization_id', 'document_type']);
            $table->index(['organization_id', 'created_at']);
            $table->index('file_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_documents');
    }
};
