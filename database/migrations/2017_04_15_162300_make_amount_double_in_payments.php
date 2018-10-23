<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeAmountDoubleInPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function(Blueprint $table) {
            $table->renameColumn('amount', 'amount_int');
        });
        Schema::table('payments', function(Blueprint $table) {
            $table->double('amount');
        });
        DB::statement("UPDATE payments SET amount=amount_int");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function(Blueprint $table) {
            $table->dropColumn('amount');
        });
        Schema::table('payments', function(Blueprint $table) {
            $table->renameColumn('amount_int', 'amount');
        });
    }
}
