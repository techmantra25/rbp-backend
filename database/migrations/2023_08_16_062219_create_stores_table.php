<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('sequence_no')->nullable();
            $table->string('unique_code')->nullable();
            $table->string('name')->nullable();
            $table->string('business_name')->nullable();
            $table->string('owner_fname')->nullable();
            $table->string('owner_lname')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('date_of_anniversary')->nullable();
            $table->string('contact_person_fname')->nullable();
            $table->string('contact_person_lname')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('contact_person_whatsapp')->nullable();
            $table->string('contact_person_date_of_birth')->nullable();
            $table->string('contact_person_date_of_anniversary')->nullable();
            $table->string('store_OCC_number')->nullable();
            $table->integer('contact');
            $table->integer('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('area')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('pin')->nullable();
            $table->string('gst')->nullable();
            $table->string('image')->nullable();
            $table->string('password')->nullable();
            $table->integer('secret_pin')->nullable();
            $table->integer('wallet')->default(0);
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
        Schema::dropIfExists('stores');
    }
}
