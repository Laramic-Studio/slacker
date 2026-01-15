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
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('display_name')->nullable();
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->string('display_name')->nullable();
            $table->foreignUlid('organisation_id')->nullable()->constrained('organisations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('display_name');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('display_name');
            $table->dropForeign(['organisation_id']);
            $table->dropColumn('organisation_id');
        });
    }
};
