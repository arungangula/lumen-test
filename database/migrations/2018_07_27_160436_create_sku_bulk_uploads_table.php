<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkuBulkUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sku_bulk_uploads', function($table) {
            $table->increments('id');
            $table->string('service_id');
            $table->string('file_path');
            $table->string('status')->default('created');
            $table->string('error_message');
            $table->integer('uploaded_by');
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sku_bulk_uploads');
    }
}
