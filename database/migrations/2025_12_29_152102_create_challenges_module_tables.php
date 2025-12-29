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
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('hashtag')->unique();
            $table->text('guidelines');
            $table->text('prizes');
            $table->timestamp('start_at');
            $table->timestamp('end_at')->nullable();
            $table->boolean('is_admin_challenge')->default(false);
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('banner_url')->nullable();
            $table->string('custom_link')->unique()->nullable();
            $table->timestamps();
        });

        Schema::create('challenge_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at');
            $table->unique(['challenge_id', 'user_id']);
        });

        Schema::create('challenge_judges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->unique(['challenge_id', 'user_id']);
            $table->timestamps();
        });

        Schema::create('challenge_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->comment('1-5 stars');
            $table->unique(['post_id', 'user_id']);
            $table->timestamps();
        });

        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon_url')->nullable();
            $table->timestamps();
        });

        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('awarded_at');
            $table->timestamps();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('challenge_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_challenge_pinned')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['challenge_id']);
            $table->dropColumn(['challenge_id', 'is_challenge_pinned']);
        });
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('challenge_ratings');
        Schema::dropIfExists('challenge_judges');
        Schema::dropIfExists('challenge_participants');
        Schema::dropIfExists('challenges');
    }
};
