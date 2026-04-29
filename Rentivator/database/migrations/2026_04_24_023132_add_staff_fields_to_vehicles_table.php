<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('driver_name')->nullable()->after('estimated_fix_days');
            $table->decimal('driver_pay', 10, 2)->nullable()->default(0)->after('driver_name');
            $table->string('helper1_name')->nullable()->after('driver_pay');
            $table->string('helper2_name')->nullable()->after('helper1_name');
            $table->string('helper3_name')->nullable()->after('helper2_name');
            $table->decimal('helper_pay_each', 10, 2)->nullable()->default(0)->after('helper3_name');
            $table->decimal('diesel_cost', 10, 2)->nullable()->default(0)->after('helper_pay_each');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'driver_name','driver_pay',
                'helper1_name','helper2_name','helper3_name',
                'helper_pay_each','diesel_cost',
            ]);
        });
    }
};