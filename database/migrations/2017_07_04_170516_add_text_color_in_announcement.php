<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTextColorInAnnouncement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table)
        {
            $table->string('text_color')->after('text')->default('#000000');
        });

        $announcements = Redis::command('keys', ['announcement:*']);
        foreach ($announcements as $key) {
            $keys[] = $key;
        }

        \Redis::pipeline(function ($pipe) use ($keys) {
            foreach ($keys as $key) {
                $pipe->del($key);

            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcements', function (Blueprint $table)
        {
            $table->dropColumn('text_color');
        });
    }
}
