<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->index('cat_id');
            $table->index('sub_cat_id');
            $table->index('collection_id');
            $table->integer('position');
            $table->integer('position_collection');
            $table->string('name');
            $table->string('image');
            $table->text('short_desc');
            $table->longText('desc');
            $table->double('price', 10, 2);
            $table->double('offer_price', 10, 2);
            $table->text('size_chart');
            $table->string('pack');
            $table->integer('pack_count');
            $table->string('master_pack');
            $table->string('size_chart_image');
            $table->integer('master_pack_count');
            $table->string('only_for');
            $table->string('slug');
            $table->text('meta_title');
            $table->text('meta_desc');
            $table->text('meta_keyword');
            $table->string('style_no');
            $table->integer('view_count');
            $table->date('last_view_count_updated_at');
            $table->tinyInteger('is_trending')->comment('1: yes, 0:no')->default(0);
            $table->tinyInteger('is_best_seller')->comment('1: yes, 0:no')->default(0);
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
        Schema::dropIfExists('products');
    }
}
