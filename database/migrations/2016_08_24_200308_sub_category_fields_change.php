<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SubCategoryFieldsChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_provider_category_mapping_new', function(Blueprint $table){
            $table->integer('preg_test_price')->default(0);
            $table->renameColumn('area_of_specialization', 'specialization');
            $table->string('qualification');
            $table->string('counselling');
            $table->renameColumn('transportation_facility', 'transportation');
            $table->float('child_teach_ratio');
            $table->tinyInteger('membership');
            $table->tinyInteger('party_packages');
            $table->integer('class_duration');
            $table->integer('price_range_min');
            $table->integer('price_range_max');
            $table->integer('child_attendant_ratio');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
