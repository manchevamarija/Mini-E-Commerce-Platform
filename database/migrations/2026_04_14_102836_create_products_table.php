<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock');
            $table->string('image_url')->nullable();
            $table->enum('status', ['draft', 'active', 'archived'])->default('active');
            $table->index(['vendor_id', 'status']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
