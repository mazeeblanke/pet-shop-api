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
            $table->uuid();
            $table->string('title', 255);
            $table->float('price', 8, 2);
            $table->string('description');
            $table->uuid('category_uuid')->index();
            $table->timestamps();
            $table->json('metadata')->nullable();
            $table->softDeletes();

            $table->foreign('category_uuid')
                ->references('uuid')->on('categories');
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
