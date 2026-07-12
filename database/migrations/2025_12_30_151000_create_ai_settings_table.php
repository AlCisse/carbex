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
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        $defaults = [
            ['key' => 'default_provider', 'value' => 'anthropic', 'type' => 'string', 'description' => 'Provider IA par défaut'],
            ['key' => 'anthropic_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Activer Anthropic (Claude)'],
            ['key' => 'anthropic_model', 'value' => 'claude-sonnet-4-20250514', 'type' => 'string', 'description' => 'Modèle Claude par défaut'],
            ['key' => 'openai_enabled', 'value' => '0', 'type' => 'boolean', 'description' => 'Activer OpenAI (GPT)'],
            ['key' => 'openai_model', 'value' => 'gpt-4o', 'type' => 'string', 'description' => 'Modèle GPT par défaut'],
            ['key' => 'google_enabled', 'value' => '0', 'type' => 'boolean', 'description' => 'Activer Google (Gemini)'],
            ['key' => 'google_model', 'value' => 'gemini-1.5-pro', 'type' => 'string', 'description' => 'Modèle Gemini par défaut'],
            ['key' => 'deepseek_enabled', 'value' => '0', 'type' => 'boolean', 'description' => 'Activer DeepSeek'],
            ['key' => 'deepseek_model', 'value' => 'deepseek-chat', 'type' => 'string', 'description' => 'Modèle DeepSeek par défaut'],
            ['key' => 'max_tokens', 'value' => '4096', 'type' => 'integer', 'description' => 'Tokens maximum par requête'],
            ['key' => 'temperature', 'value' => '0.7', 'type' => 'string', 'description' => 'Température (créativité)'],
        ];

        foreach ($defaults as $setting) {
            \DB::table('ai_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
