<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomCompleteToTest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->tinyInteger('custom_completion_status')->nullable();
            $table->longtext('custom_completion_style')->nullable();
            $table->longtext('custom_completion_body')->nullable();
            $table->string('custom_completion_background')->nullable();
            $table->string('custom_completion_header')->nullable();
            $table->string('custom_completion_badge')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('tests', 'custom_completion_status')) {
            Schema::table('tests', function (Blueprint $table) {
                $table->dropColumn('custom_completion_status');
            });
        }
        if (Schema::hasColumn('tests', 'custom_completion_style')) {
            Schema::table('tests', function (Blueprint $table) {
                $table->dropColumn('custom_completion_style');
            });
        }
        if (Schema::hasColumn('tests', 'custom_completion_body')) {
            Schema::table('tests', function (Blueprint $table) {
                $table->dropColumn('custom_completion_body');
            });
        }
        if (Schema::hasColumn('tests', 'custom_completion_background')) {
            Schema::table('tests', function (Blueprint $table) {
                $table->dropColumn('custom_completion_background');
            });
        }
        if (Schema::hasColumn('tests', 'custom_completion_header')) {
            Schema::table('tests', function (Blueprint $table) {
                $table->dropColumn('custom_completion_header');
            });
        }
        if (Schema::hasColumn('tests', 'custom_completion_badge')) {
            Schema::table('tests', function (Blueprint $table) {
                $table->dropColumn('custom_completion_badge');
            });
        }
    }
}
