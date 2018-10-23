<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShippingChargeCommissionModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_services_providers_new', function (Blueprint $table) {
            $table->double('advance_percent')->default(100.0);
            $table->boolean('override_system_commission_percent')->default(false);
            $table->double('commission_percent');
            $table->double('commission_cutoff');
            $table->double('commission_percent_above_cutoff');

            $table->double('shipping_charge');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('override_service_shipping_charge')->default(false);
            $table->double('shipping_charge');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_services_providers_new', function (Blueprint $table) {
            $table->dropColumn('advance_percent');
            $table->dropColumn('override_system_commission_percent');
            $table->dropColumn('commission_percent');
            $table->dropColumn('commission_cutoff');
            $table->dropColumn('commission_percent_above_cutoff');

            $table->dropColumn('shipping_charge');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('override_service_shipping_charge');
            $table->dropColumn('shipping_charge');
        });
    }
}
