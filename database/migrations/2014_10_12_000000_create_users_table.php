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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 150)->unique()->nullable();
            $table->string('password', 256);
            $table->string('phone', 20);
            $table->string('profile_uri', 255)->nullable();
            $table->timestamp('last_password_reset')->nullable();
            $table->enum('status', ['Active', 'InActive'])->default('Active');
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->foreign('client_id')->references('id')->on('clients');
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
