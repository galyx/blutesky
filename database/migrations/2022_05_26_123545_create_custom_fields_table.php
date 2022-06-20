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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->integer('job_id')->nullable();
            $table->integer('list_id')->nullable();
            $table->integer('position')->nullable();
            $table->string('label_field_id')->nullable();
            $table->string('field_type')->nullable();
            $table->string('field_mask')->nullable();
            $table->string('list_name')->nullable();
            $table->string('label_field')->nullable();
            $table->integer('field_edit_lists')->nullable();
            $table->integer('field_required')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('custom_fields');
    }
};
