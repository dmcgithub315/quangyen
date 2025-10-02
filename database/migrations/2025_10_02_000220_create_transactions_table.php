<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // income | expense | payroll | other
            $table->string('category')->nullable(); // 'salary', 'transport', 'utilities', 'rent', 'other'
            $table->foreignId('staff_id')->nullable()->constrained('staffs')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('method')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('related_date')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};


