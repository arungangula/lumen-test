<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBcContentLifestagesMappingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bc_content_lifestages_mapping', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('article_id');
			$table->integer('lifestage_value');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bc_content_lifestages_mapping');
	}

}
