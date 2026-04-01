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
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            // Foreign key linking to users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('accessory_name');
            $table->decimal('buying_amount', 10, 2);
            $table->decimal('selling_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0); // Tracking how much the customer paid

            // Customer details (nullable as requested)
            $table->string('name')->nullable();
            $table->string('contact')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};
