<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBabyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("babies", function($table) {
            $table->increments("id");
            $table->string("name", 100);
            $table->string("gender", 10);
          	$table->date('birth_date');
            $table->integer('parent_id')->nullable();
            $table->timestamps();
        });

         Schema::table('babies', function($table) {
               $table->foreign('parent_id')->references('id')->on('bc_users');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('babies');
	}

}
