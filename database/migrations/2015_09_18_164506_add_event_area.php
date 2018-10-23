<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Models\Event;

class AddEventArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //add column event_area_id
        Schema::table('events_master', function ($table) {
            $table->integer('event_area_id')->after('event_city');
        });

        //update area_id and city_id
        $locations = Location::all();
        foreach($locations as $location){
           Event::where('event_location', $location->id)->update([ 'event_area_id' => $location->area_id , 'event_city' => $location->city_id ]);
        } 
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
