<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $row) {
            $row->id();
            $row->foreignId('category_id')->constrained()->onDelete('cascade');
            $row->string('name');
            $row->string('sku')->unique();
            $row->text('description')->nullable();
            $row->decimal('price', 15, 2);
            $row->decimal('cost_price', 15, 2)->nullable();
            $row->integer('stock_quantity')->default(0);
            $row->integer('alert_quantity')->default(10);
            $row->boolean('is_active')->default(true);
            $row->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
