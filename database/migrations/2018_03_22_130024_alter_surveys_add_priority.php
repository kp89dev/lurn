<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSurveysAddPriority extends Migration
{
    protected $columns = [
        'priority',
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
                $table->boolean('priority')->default(0);
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
                $table->dropColumn(['priority']);
            });
        }
    }
}
