<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSeoDefaultsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_defaults', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('site_name');
            $table->char('separator', 1);
            $table->text('description');
            $table->text('keywords')->nullable();
            $table->boolean('robots')->default(false);
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->string('webmasters_google')->nullable();
            $table->string('webmasters_bing')->nullable();
            $table->string('webmasters_alexa')->nullable();
            $table->string('webmasters_pinterest')->nullable();
            $table->string('webmasters_yandex')->nullable();
            $table->boolean('og_enabled')->default(false);
            $table->string('og_prefix');
            $table->string('og_type');
            $table->string('og_title');
            $table->string('og_site_name');
            $table->string('og_description');
            $table->text('og_properties')->nullable();
            $table->boolean('twitter_enabled')->default(false);
            $table->string('twitter_card');
            $table->string('twitter_site');
            $table->string('twitter_title');
            $table->text('twitter_meta')->nullable();
            $table->string('analytics_google')->nullable();
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
        Schema::dropIfExists('seo_defaults');
    }
}
