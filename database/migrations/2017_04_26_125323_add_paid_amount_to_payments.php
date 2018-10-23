<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaidAmountToPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments_new', function (Blueprint $table)
        {
            $table->double('paid_amount');
            $table->double('refund_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments_new', function (Blueprint $table)
        {
            $table->dropColumn('paid_amount');
            $table->dropColumn('refund_amount');
        });
    }
}
