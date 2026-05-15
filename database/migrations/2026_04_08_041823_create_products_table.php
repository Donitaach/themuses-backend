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

            // 🔥 FIELD UTAMA
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('category_id');
            $table->string('image_url')->nullable();
            $table->integer('stock');

            // 💎 KHUSUS PERHIASAN
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('material')->nullable();
            $table->string('gemstone')->nullable();
            $table->string('size')->nullable();

            $table->timestamps();

            // 🔗 FOREIGN KEY
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
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