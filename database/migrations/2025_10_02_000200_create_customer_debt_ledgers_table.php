<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_debt_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('bill_id')->nullable()->constrained('bills')->nullOnDelete();
            $table->foreignId('repayment_id')->nullable()->constrained('repayment_histories')->nullOnDelete();
            $table->string('type'); // bill | repayment | adjustment
            $table->decimal('amount', 15, 2); // dương: tăng nợ, âm: giảm nợ
            $table->decimal('balance_after', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_debt_ledgers');
    }
};


