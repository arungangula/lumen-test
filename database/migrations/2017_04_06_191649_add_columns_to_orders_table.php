<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function(Blueprint $table) {
            $table->integer('user_id')->change();
            $table->integer('service_id');
            $table->string('invoice_number');
            $table->index('user_id');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function(Blueprint $table) {
            $table->integer('user_id')->unsigned()->change();
            $table->dropColumn('service_id');
            $table->dropColumn('invoice_number');
            $table->dropIndex('service_id');
            $table->dropIndex('user_id');
        });
    }
}
