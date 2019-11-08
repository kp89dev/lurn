<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_descriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('description_type_id');
            $table->text('description');
            $table->timestamps();

            $table->foreign('description_type_id')
                ->references('id')
                ->on('description_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_descriptions');
    }
}
