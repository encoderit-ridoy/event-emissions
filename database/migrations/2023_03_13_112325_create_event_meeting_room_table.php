<?php

use App\Models\Event;
use App\Models\MeetingRoomManagement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventMeetingRoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_meeting_room', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Event::class);
            $table->foreignIdFor(MeetingRoomManagement::class);
            $table->float('meeting_time', 8, 2)->nullable();
            $table->text('mr_assumption')->nullable();
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
        Schema::dropIfExists('event_meeting_room');
    }
}
