<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToTables extends Migration
{
    public function up()
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_groups', 'processed_customers_data')) {
                $table->longText('processed_customers_data')->nullable();
            }
            if (!Schema::hasColumn('customer_groups', 'total_customers')) {
                $table->integer('total_customers')->default(0);
            }
        });

        Schema::table('customer_product_purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_product_purchases', 'schedule')) {
                $table->date('schedule')->nullable();
            }
            if (!Schema::hasColumn('customer_product_purchases', 'time_send')) {
                $table->time('time_send')->nullable();
            }
            if (!Schema::hasColumn('customer_product_purchases', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            $table->dropColumn(['processed_customers_data', 'total_customers']);
        });

        Schema::table('customer_product_purchases', function (Blueprint $table) {
            $table->dropColumn(['schedule', 'time_send', 'created_at', 'updated_at']);
        });
    }
}