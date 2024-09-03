<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mk_rekening', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('branch_id');
            $table->string('name');
            $table->string('bank');
            $table->string('no_rekening');
            $table->string('description');
            $table->boolean('status')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('mk_rekening');
    }
};
