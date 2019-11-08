<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersImportAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_import_all', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('from_table');
            $table->tinyInteger('role_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('md5password')->nullable();
            $table->string('salt')->nullable();
            $table->text('description')->nullable();
            $table->smallInteger('status')->nullable();
            $table->integer('infusion_order_id')->nullable();
            $table->integer('infusion_contact_id')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->tinyInteger('unsubscribe')->defaults(0)->nullable();
            $table->text('settings')->nullable();

            $table->unique(['id', 'from_table']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users_import_all');
    }
}
