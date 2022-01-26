<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSchedulingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_schedulings', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('external_id');
            $table->string('from')->nullable();
            $table->string('from_name')->nullable();
            $table->string('to');
            $table->string('to_name')->nullable();
            $table->string('subject');
            $table->string('body')->nullable();
            $table->string('template')->nullable();
            $table->string('template_variables')->nullable();
            $table->boolean('sent');
            $table->date('delivery_date');
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
        Schema::dropIfExists('email_schedulings');
    }
}
