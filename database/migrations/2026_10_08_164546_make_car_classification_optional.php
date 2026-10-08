<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars_car', function (Blueprint $table): void {
            $table->unsignedBigInteger('brand_id')->nullable()->change();
            $table->unsignedBigInteger('category_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('cars_car')->whereNull('brand_id')->orWhereNull('category_id')->exists()) {
            throw new LogicException('Before rolling back, assign a brand and category to every car.');
        }

        Schema::table('cars_car', function (Blueprint $table): void {
            $table->unsignedBigInteger('brand_id')->nullable(false)->change();
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
        });
    }
};
