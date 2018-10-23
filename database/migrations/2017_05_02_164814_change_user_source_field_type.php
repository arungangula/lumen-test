<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUserSourceFieldType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function(Blueprint $table) {
            $table->dropColumn('user_source');
        });
        Schema::table('orders', function(Blueprint $table) {
            $table->string('user_source', 20);
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
            $table->dropColumn('user_source');
        });
        Schema::table('orders', function(Blueprint $table) {
            $table->double('user_source');
        });
    }
}
