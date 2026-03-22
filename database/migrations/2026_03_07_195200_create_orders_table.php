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
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();

            // Guest checkout info
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            // Waktu pengambilan & catatan
            $table->dateTime('pickup_time');
            $table->text('notes')->nullable();

            // Kalkulasi
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);

            // Voucher
            $table->foreignId('discount_id')->nullable()->constrained()->nullOnDelete();

            // Status
            $table->enum('status', [
                'pending',      // baru dibuat
                'confirmed',    // dikonfirmasi admin
                'processing',   // sedang diproses
                'ready',        // siap diambil
                'completed',    // selesai
                'cancelled',    // dibatalkan
            ])->default('pending');

            $table->enum('payment_status', [
                'unpaid',
                'paid',
                'failed',
                'expired',
                'refunded',
            ])->default('unpaid');
            $table->string('payment_method')->default('cash');


            // Waktu estimasi & selesai
            $table->dateTime('estimated_ready_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Index untuk pencarian & laporan
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
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
