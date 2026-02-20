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
        Schema::create('popups', function (Blueprint $table) {
            $table->id();

            // Content
            $table->string('name')->comment('Internal name for admin');
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('image')->nullable();

            // Button
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('button_color')->default('#dc2626'); // red-600

            // Styling
            $table->enum('size', ['small', 'medium', 'large'])->default('medium');
            $table->enum('position', ['center', 'bottom-right', 'bottom-left'])->default('center');
            $table->string('background_color')->default('#ffffff');
            $table->string('text_color')->default('#1f2937');
            $table->boolean('show_overlay')->default(true);
            $table->boolean('closable')->default(true);

            // Display rules
            $table->boolean('is_active')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Targeting
            $table->json('show_on_pages')->nullable()->comment('null = all pages, or array of page types');
            $table->boolean('show_on_mobile')->default(true);
            $table->boolean('show_on_desktop')->default(true);

            // Frequency
            $table->enum('frequency', ['always', 'once_per_session', 'once_per_day', 'once_ever'])->default('once_per_session');
            $table->integer('delay_seconds')->default(2)->comment('Delay before showing');

            // Priority (higher = shown first if multiple)
            $table->integer('priority')->default(0);

            // Stats
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('clicks_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popups');
    }
};
