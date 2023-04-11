<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->onDelete('cascade');
            $table->string('name');
            $table->string('event_holder');
            $table->string('starting_date');
            $table->string('end_date');
            $table->string('people_in_charge');
            $table->text('mr_assumption')->nullable();
            $table->text('tp_assumption')->nullable();
            $table->text('om_assumption')->nullable();
            $table->text('oa_assumption')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('events');
    }
}
