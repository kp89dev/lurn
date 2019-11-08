<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNicheToolCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE TABLE `nd_categories` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `main_offer` float DEFAULT \'0\',
              `upsell1` float DEFAULT NULL,
              `upsell2` float DEFAULT NULL,
              `order` int(5) DEFAULT NULL,
              `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `created_at` datetime NOT NULL,
              `updated_date` datetime NOT NULL,
              PRIMARY KEY (`id`),
              KEY `label` (`label`),
              KEY `lastModified` (`updated_date`)
            ) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
        );

        DB::insert("insert  into `nd_categories`(`id`,`label`,`main_offer`,`upsell1`,`upsell2`,`order`,`image`,`created_at`,`updated_date`) values 
            (54,'Health & Fitness',19.48,27,17,0,NULL,'0000-00-00 00:00:00','2015-09-18 15:15:22'),
            (56,'Dating & Relationships',47,97,47,1,NULL,'0000-00-00 00:00:00','2015-09-18 15:16:29'),
            (57,'Money & Success',97,47,17,2,NULL,'0000-00-00 00:00:00','2015-09-18 15:17:03'),
            (58,'Self Help',37,45,27,3,NULL,'0000-00-00 00:00:00','2015-09-18 12:46:43');
         ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nd_categories');
    }
}
