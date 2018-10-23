<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWarehouseDetailsToService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_services_providers_new', function($table) {
            $table->integer('warehouse_check');
            $table->string('warehouse_city_name');
            $table->string('warehouse_pincode');
            $table->text('warehouse_address');
            $table->text('warehouse_contact_person');
            $table->string('warehouse_email');
            $table->string('warehouse_phone');
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
            $table->dropColumn('warehouse_check');
            $table->dropColumn('warehouse_city_name');
            $table->dropColumn('warehouse_pincode');
            $table->dropColumn('warehouse_address');
            $table->dropColumn('warehouse_contact_person');
            $table->dropColumn('warehouse_email');
        });
    }
}
