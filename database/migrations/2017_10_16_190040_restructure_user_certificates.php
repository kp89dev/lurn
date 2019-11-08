<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestructureUserCertificates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_certificates', function (Blueprint $table) {
            $table->renameColumn('certificate_image', 'certificate_logo');
            $table->string('certificate_logo_style'); 
            $table->renameColumn('certificate_image1', 'certificate_border');
            $table->string('certificate_border_style');
            $table->renameColumn('certificate_image2', 'certificate_background');
            $table->string('certificate_sign')->nullable();
            $table->string('certificate_sign_style')->nullable();
            $table->string('certificate_badge')->nullable();
            $table->string('certificate_badge_style')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_certificates', function (Blueprint $table) {
            //
        });
    }
}
