<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnFromTableImportUsersAll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_import_all', function (Blueprint $table) {
            $table->renameColumn('from_table', 'connection');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_import_all', function (Blueprint $table) {
            $table->renameColumn('connection', 'from_table');
        });
    }
}
