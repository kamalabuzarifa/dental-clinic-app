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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('show_on_website')->default(true)->after('is_active');
            $table->string('specialty')->nullable()->after('show_on_website');
            $table->text('bio')->nullable()->after('specialty');
            $table->string('avatar')->nullable()->after('bio');
            $table->integer('sort_order')->default(0)->after('avatar');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('duration_minutes');
            $table->boolean('show_on_website')->default(true)->after('is_active');
            $table->boolean('is_featured')->default(false)->after('show_on_website');
            $table->string('icon')->nullable()->after('is_featured');
            $table->integer('sort_order')->default(0)->after('icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['show_on_website', 'specialty', 'bio', 'avatar', 'sort_order']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'show_on_website', 'is_featured', 'icon', 'sort_order']);
        });
    }
};
