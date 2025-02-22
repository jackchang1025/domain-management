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
        Schema::table('chains', function (Blueprint $table) {
            $table->string('domain')->nullable()->change();
            $table->string('domain_url')->nullable()->change();
            $table->string('type')->nullable()->change();
            $table->string('sub_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chains', function (Blueprint $table) {
            $table->string('domain')->nullable(false)->change();
            $table->string('domain_url')->nullable(false)->change();
            $table->string('type')->nullable(false)->change();
            $table->string('sub_type')->nullable(false)->change();
        });
    }
};
