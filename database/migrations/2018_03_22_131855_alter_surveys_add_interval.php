<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSurveysAddInterval extends Migration
{
    protected $columns = [
        'interval',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('surveys', $this->columns)) {
            Schema::table('surveys', function (Blueprint $table) {
                $table->unsignedBigInteger('interval')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumns('surveys', $this->columns)) {
            Schema::table('surveys', function (Blueprint $table) {
                $table->dropColumn(['interval']);
            });
        }
    }
}
