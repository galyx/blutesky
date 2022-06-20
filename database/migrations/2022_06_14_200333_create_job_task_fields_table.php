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
        Schema::create('job_task_fields', function (Blueprint $table) {
            $table->integer('job_id')->nullable();
            $table->integer('list_id')->nullable();
            $table->integer('task_id')->nullable();
            $table->string('field_label_id')->nullable();
            $table->string('field_value')->nullable();
            $table->string('field_array')->nullable();
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
        Schema::dropIfExists('job_task_fields');
    }
};
