<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestructureCertificates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->renameColumn('image', 'logo');
            $table->string('logo_style')->nullable();            
            $table->renameColumn('image1', 'border');
            $table->string('border_style')->nullable();
            $table->renameColumn('image2', 'background');
            $table->string('sign')->nullable();
            $table->string('sign_style')->nullable();
            $table->string('badge')->nullable();
            $table->string('badge_style')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certificates', function (Blueprint $table) {
            //
        });
    }
}
