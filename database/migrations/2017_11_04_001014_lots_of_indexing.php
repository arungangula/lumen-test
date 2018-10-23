<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LotsOfIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo "\n notifications: ".date('Y-m-d H:i:s', time());
        //notifications
        Schema::table('notifications', function($table){
            $table->index('entity_id');
            $table->index('notification_type');
            $table->index('notification_state');
            $table->index('trigger_user_id');
            $table->index('notification_expiry');
            $table->index('created_at');
            $table->index('display_time');
        });

        echo "\n gcm_push: ".date('Y-m-d H:i:s', time());
        //gcm_push
        Schema::table('gcm_push', function($table){
            $table->index('sitem_id');
            $table->index('created_at');
            $table->index('schedule_at');
            $table->index('target_user_age_from');
            $table->index('target_user_age_to');
        });

        echo "\n gcm_push_mapping: ".date('Y-m-d H:i:s', time());
        //gcm_push_mapping
        Schema::table('gcm_push_mapping', function($table){
            $table->index('gcm_push_id');
            $table->index('lifestage_id');
            $table->index('location_id');
            $table->index('area_id');
            $table->index('created_at');
            $table->index('lifestage_period');
        }); 

        echo "\n devices: ".date('Y-m-d H:i:s', time());
        //devices
        Schema::table('registered_devices', function($table){
            $table->index('cloud_token');
            $table->index('device_uid');
            $table->index('channel');
            $table->index('created_at');
            $table->index('one_signal_id');
            $table->index('app_version');
        });

        echo "\n push_logs: ".date('Y-m-d H:i:s', time());
        //push_notification_logs
        Schema::table('push_notification_log', function($table){
            $table->index('element_id');
            $table->index('element_type');
            $table->index('success');
            $table->index('failure');
            $table->index('created_at');
            $table->index('converted');
            $table->index('remaining');
        });

        echo "\n app_open_logs: ".date('Y-m-d H:i:s', time());
        //app_open_logs
        Schema::table('app_open_logs', function($table){
            $table->index('device_id');
            $table->index('created_at');
            $table->index('android_id');
            $table->index('gcm_token');
        });

        echo "\n post_likes: ".date('Y-m-d H:i:s', time());
        //post_likes
        Schema::table('post_likes', function($table){
            $table->index('element_id');
            $table->index('element_type');
            $table->index('created_at');
        });

        echo "\n bc_commentable: ".date('Y-m-d H:i:s', time());
        //post_likes
        Schema::table('bc_commentable', function($table){
            $table->index('created_at');
            $table->index('published');
            $table->index('anonymous');
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
