<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('status');
            $table->text('showcase_description')->nullable()->after('is_public');
            $table->json('showcase_photos')->nullable()->after('showcase_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'showcase_description', 'showcase_photos']);
        });
    }
};
