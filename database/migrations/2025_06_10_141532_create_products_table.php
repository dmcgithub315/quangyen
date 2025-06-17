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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Basic product information
            $table->string('name');
            $table->text('description')->nullable();
            // $table->string('slug')->unique();

            // Category relationship
            // $table->unsignedBigInteger('category_id')->nullable();
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            // Product specifications
            // $table->string('type')->nullable();
            // $table->string('standard')->nullable();
            // $table->string('detail_standard')->nullable();
            // $table->string('grade')->nullable();
            $table->string('origin')->nullable();
            // $table->string('size')->nullable();
            // $table->string('thickness')->nullable();
            // $table->string('length')->nullable();
            // $table->string('advantage')->nullable();
            // $table->string('surface')->nullable();

            // Media and rating
            // $table->json('album')->nullable(); // JSON array for multiple images
            // $table->decimal('rating', 3, 2)->default(0.00); // Rating from 0.00 to 9.99

            // Pricing (optional)
            $table->integer('price')->nullable();
            // $table->decimal('sale_price', 15, 2)->nullable();
            $table->string('price_unit')->nullable(); // đơn vị tính: kg, m, tấn, etc.

            // Stock management
            $table->integer('stock_quantity')->default(0);
            // $table->boolean('manage_stock')->default(false);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'on_backorder'])->default('in_stock');

            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
