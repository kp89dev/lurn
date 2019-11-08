<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_emails', function (Blueprint $table) {
            $table->integer('user_id');
            $table->string('email');

            $table->primary(['user_id', 'email']);
        });

        // Grab the current emails.
        DB::statement("insert into user_emails (select id as user_id, email from users)");

        // Create the email recording trigger.
        DB::statement("
            create trigger record_user_emails after update on users
            for each row
            begin
                if (new.email != old.email) then
                    insert ignore into user_emails values (old.id, new.email);
                end if;
            end
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_emails');
        DB::statement('drop trigger if exists record_user_emails');
    }
}
