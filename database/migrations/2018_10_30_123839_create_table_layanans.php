<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLayanans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layanans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jenis_id')->unsigned();
            $table->foreign('jenis_id')->references('id')->on('jenis_layanans')
                ->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->string('paket');
            $table->string('harga');
            $table->integer('diskon')->length(3);
            $table->boolean('isHours')->default(false);
            $table->integer('hours')->nullable();
            $table->integer('price_per_hours')->nullable();
            $table->boolean('isQty')->default(false);
            $table->integer('qty')->nullable();
            $table->integer('price_per_qty')->nullable();
            $table->boolean('isStudio')->default(false);
            $table->text('keuntungan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('layanans');
    }
}
