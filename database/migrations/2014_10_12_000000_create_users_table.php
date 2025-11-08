<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('mobile')->unique();
            $table->integer('whatsapp_no')->unique();
            $table->string('password');
            $table->string('employee_id')->unique();
            $table->integer('address')->nullable();
            $table->integer('landmark')->nullable();
            $table->integer('state')->nullable();
            $table->integer('city')->nullable();
            $table->integer('pin')->nullable();
            $table->integer('aadhar_no')->unique();
            $table->integer('pan_no')->unique();
            $table->integer('type')->nullable()->comment('1: NSM, 2: ZSM , 3:RSM,4:SM, 5 :ASM , 6:ASE');
            $table->string('otp')->nullable();
            $table->string('image')->nullable();
            $table->string('gender', 30)->nullable();
            $table->string('social_id')->nullable();
            $table->integer('is_verified')->comment('1: verified, 0: not verified')->default(0);
            $table->integer('status')->comment('1: active, 0: inactive')->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
