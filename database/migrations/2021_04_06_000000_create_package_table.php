<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('package_table', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            // add fields
        });
    }
};
