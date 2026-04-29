<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@rentivator.com'],
            [
                'name'       => 'Admin',
                'email'      => 'admin@rentivator.com',
                'password'   => Hash::make('admin'),
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'admin@rentivator.com')->delete();
    }
};