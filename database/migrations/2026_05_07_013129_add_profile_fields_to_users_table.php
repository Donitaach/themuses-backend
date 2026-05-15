<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run migrations
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // nomor hp
            $table->string('phone')->nullable();

            // alamat
            $table->text('address')->nullable();

            // avatar profile
            $table->string('avatar')->nullable();
        });
    }

    /**
     * Reverse migrations
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'phone',
                'address',
                'avatar'
            ]);
        });
    }
};