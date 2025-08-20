<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('status', 32);          // ex.: 'captured'
            $table->string('method', 32)->nullable(); // ex.: 'card'
            $table->string('transaction_id', 128)->index(); // id do PI/charge
            $table->json('meta')->nullable();      // brand, last4, receipt URL...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
