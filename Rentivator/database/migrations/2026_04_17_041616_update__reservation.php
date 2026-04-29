<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('reservations', 'end_date')) {
                $table->dropColumn('end_date');
            }
            if (!Schema::hasColumn('reservations', 'reservation_date')) {
                $table->date('reservation_date')->nullable()->after('hectares');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'reservation_date')) {
                $table->dropColumn('reservation_date');
            }
            $table->date('start_date')->nullable()->after('hectares');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }
};