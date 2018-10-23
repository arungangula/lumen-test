<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PersonalizedCardTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personalized_card_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('template_type')->default('generic_message');
            $table->text('image_url');
            $table->text('personalized_message');
            $table->text('cta_text');
            $table->string('cta_normal_bg', 50);
            $table->string('cta_pressed_bg', 50);
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
        Schema::drop('personalized_card_template');
    }
}
