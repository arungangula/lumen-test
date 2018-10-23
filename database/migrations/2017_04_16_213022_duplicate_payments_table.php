<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DuplicatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE TABLE payments_new LIKE payments; ');
        Schema::table('payments_new', function (Blueprint $table)
        {
            $table->dropColumn('service_id');
            $table->dropColumn('amount_int');
            $table->dropColumn('fee');
            $table->dropColumn('service_tax');
            $table->dropColumn('razor_timestamp');
            $table->dropColumn('package_id');
            $table->dropColumn('package_avail_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payments_new');
    }
}
