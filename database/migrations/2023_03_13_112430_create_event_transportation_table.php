<?php

use App\Models\Event;
use App\Models\TransportationManagement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTransportationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_transportation', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Event::class);
            $table->foreignIdFor(TransportationManagement::class);
            $table->integer('no_of_people')->nullable();
            $table->float('transportation_fee')->nullable();
            $table->text('tp_assumption')->nullable();
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
        Schema::dropIfExists('event_transportation');
    }
}
