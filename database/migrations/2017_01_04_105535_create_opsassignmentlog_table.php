<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpsassignmentlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ops_service_assignments_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ops_assignment_id');
            $table->integer('user_id');
            $table->string('user_name');
            $table->string('status_before');
            $table->string('status_now');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ops_service_assignments_logs');
    }
}
