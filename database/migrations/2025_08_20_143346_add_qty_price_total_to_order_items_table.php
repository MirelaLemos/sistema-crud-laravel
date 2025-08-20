<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'qty')) {
                $table->unsignedInteger('qty')->default(1)->after('product_id');
            }
            if (!Schema::hasColumn('order_items', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->default(0)->after('qty');
            }
            if (!Schema::hasColumn('order_items', 'total')) {
                $table->decimal('total', 10, 2)->default(0)->after('unit_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'total')) $table->dropColumn('total');
            if (Schema::hasColumn('order_items', 'unit_price')) $table->dropColumn('unit_price');
            if (Schema::hasColumn('order_items', 'qty')) $table->dropColumn('qty');
        });
    }
};

