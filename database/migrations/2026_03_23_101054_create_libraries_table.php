<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('libraries', function (Blueprint $table) {
            $table->id();
            // Constrained foreign key (links to users table)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('type'); // e.g., 'Series', 'Movie', 'Songs'
            $table->decimal('amount', 10, 2); // Handles money/amounts accurately
            $table->boolean('is_debt')->default(false);
            $table->string('debtor_name')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('libraries');
    }
};