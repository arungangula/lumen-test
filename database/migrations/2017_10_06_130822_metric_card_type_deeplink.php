<?php

use App\Models\Metric;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MetricCardTypeDeeplink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('bc_metrics', function (Blueprint $table) {
            $table->string('metric_category', 50)->default(Metric::CATEGORY_METRIC);
            $table->string('cta_deeplink')->default('');
            $table->string('image_deeplink')->default('');
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
