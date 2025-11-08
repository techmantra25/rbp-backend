<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductColorSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_color_sizes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->string('product_style_no')->nullable();
            $table->bigInteger('color_id')->nullable();
            $table->string('color_fabric')->nullable();
            $table->bigInteger('size_id')->nullable();
            $table->tinyInteger('assorted_flag')->default(0);
            $table->double('price')->nullable();
            $table->double('offer_price')->nullable();
            $table->integer('stock')->nullable();
            $table->integer('position')->nullable();
            $table->tinyInteger('status')->comment('1: active, 0: inactive')->default(1);
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
        Schema::dropIfExists('product_color_sizes');
    }
}
