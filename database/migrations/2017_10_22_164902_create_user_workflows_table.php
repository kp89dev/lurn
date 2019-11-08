<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_workflows', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('workflow_id');
            $table->boolean('hit_goal')->nullable()->defaults(0);
            $table->string('next_step');
            $table->timestamp('next_step_time')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['user_id', 'workflow_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_workflows');
    }
}
