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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // e.g., ORD-2025-001
            $table->foreignId('user_id');
            $table->decimal('total', 10, 2); // Total amount (e.g., 150.50)
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_mode', ['cash', 'card', 'upi', 'paypal', 'other'])->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->json('items'); // Store product details (e.g., [{product_id, name, quantity, price}])
            $table->text('delivery_address')->nullable(); // Delivery address
            $table->enum('order_type', ['delivery', 'takeaway', 'dine_in'])->default('delivery');
            $table->text('notes')->nullable(); // Customer notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
