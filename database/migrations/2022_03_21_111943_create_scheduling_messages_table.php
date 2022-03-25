<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulingMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduling_messages', function (Blueprint $table) {
            $table->uuid('id');
            $table->json('classes');
            $table->json('conditions_stop')->nullable();
            $table->json('conditions_update')->nullable();
            $table->string('operation');
            $table->date('delivery_date');
            $table->boolean('processed')->default(false);
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
        Schema::dropIfExists('scheduling_messages');
    }
}
