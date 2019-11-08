<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackerTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_visits', function (Blueprint $table) {
            $table->increments('id');
            $table->char('visitor_id', 12);
            $table->unsignedInteger('campaign_id')->nullable();

            $table->string('referer_keyword', 255)->nullable();
            $table->string('referer_name', 70)->nullable();
            $table->boolean('referer_type')->nullable();
            $table->text('referer_url')->nullable();

            $table->string('browser_lang', 20)->nullable();
            $table->string('browser_engine', 10)->nullable();
            $table->string('browser_name', 10)->nullable();
            $table->string('browser_version', 20)->nullable();

            $table->string('device_brand', 100)->nullable();
            $table->string('device_model', 100)->nullable();
            $table->string('device_resolution')->nullable();
            $table->string('device_type')->nullable();

            $table->char('os', 3)->nullable();
            $table->string('os_version', 100)->nullable();
            $table->string('os_platform', 20)->nullable();

            $table->string('city')->nullable();
            $table->string('country_iso')->nullable();
            $table->string('country_name')->nullable();
            $table->string('continent_iso')->nullable();
            $table->string('continent_name')->nullable();
            $table->string('time_zone', 20)->nullable();
            $table->string('region_iso', 20)->nullable();
            $table->string('region_name', 25)->nullable();

            $table->text('page_uri')->nullable();
            $table->string('page_domain')->nullable();
            $table->string('page_title')->nullable();
            $table->text('page_url')->nullable();

            $table->string('event_name');

            $table->string('custom_var_k1')->nullable();
            $table->string('custom_var_v1')->nullable();

            $table->string('custom_var_k2')->nullable();
            $table->string('custom_var_v2')->nullable();

            $table->string('custom_var_k3')->nullable();
            $table->string('custom_var_v3')->nullable();

            $table->string('custom_var_k4')->nullable();
            $table->string('custom_var_v4')->nullable();

            $table->string('custom_var_k5')->nullable();
            $table->string('custom_var_v5')->nullable();

            $table->timestamps();
        });

        Schema::create('tr_identities', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->char('visitor_id', 12);
            $table->string('email');

            $table->timestamps();
        });

        Schema::create('tr_campaigns', function(Blueprint $table) {
            $table->increments('id');
            $table->char('hash');

            $table->string('name')->nullable();
            $table->string('source')->nullable();
            $table->string('medium')->nullable();
            $table->string('content')->nullable();
            $table->string('term')->nullable();

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
        Schema::dropIfExists('tr_visits');
        Schema::dropIfExists('tr_identities');
        Schema::dropIfExists('tr_campaigns');
    }
}
