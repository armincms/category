<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Armincms\Tagging\Config;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::create('categories', function (Blueprint $table) { 
            $table->bigIncrements('id');  
            $table->unsignedBigInteger('category_id')->nullable()->index(); 
            $table->string('resource');  
            $table->publication();  
            $table->hits();      
            $table->integer('depth')->default(0);  
            $table->config(json_encode(Config::default()));  
            $table->timestamps();  

            $table->foreign('category_id')->references('id')->on('categories')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('categories');
    }
}
