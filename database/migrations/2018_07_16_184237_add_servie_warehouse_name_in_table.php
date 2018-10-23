<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServieWarehouseNameInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_services_providers_new', function($table) {
            $table->string('warehouse_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_services_providers_new', function($table) {
            $table->dropColumn('warehouse_name');
        });
    }
}
