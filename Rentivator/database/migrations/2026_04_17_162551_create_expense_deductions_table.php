<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_expense_deductions_table.php
public function up(): void
{
    Schema::create('expense_deductions', function (Blueprint $table) {
        $table->id();
        $table->decimal('amount', 10, 2);
        $table->string('reason');
        $table->date('deduction_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_deductions');
    }
};
