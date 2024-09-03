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
        Schema::create('t_deposit_mutasi', function (Blueprint $table) {
            $table->foreignId('deposit_id')
                ->references('id')
                ->on('t_deposit')
                ->onDelete('cascade');
            $table->integer('id');
            $table->enum('jenis_deposit', ['DEBET', 'KREDIT']);
            $table->double('nilai', 20, 2);
            $table->text('keterangan')->nullable();
            $table->primary(['deposit_id', 'id']);
            $table->string('created_by');
            $table->string('updated_by');
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
        Schema::dropIfExists('t_deposit_mutasi');
    }
};
