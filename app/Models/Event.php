<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'event_holder',
        'starting_date',
        'end_date',
        'people_in_charge',
        'mr_assumption',
        'tp_assumption',
        'om_assumption',
        'oa_assumption',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function meetingRooms()
    {
        return $this->belongsToMany(MeetingRoomManagement::class, 'event_meeting_room', 'event_id', 'meeting_room_management_id')->withPivot('meeting_time')->withTimestamps();
    }

    public function transportations()
    {
        return $this->belongsToMany(TransportationManagement::class, 'event_transportation', 'event_id', 'transportation_management_id')->withPivot('no_of_people', 'transportation_fee')->withTimestamps();
    }

    public function onlineMeetings()
    {
        return $this->belongsToMany(OnlineMeetingManagement::class, 'event_online_meeting', 'event_id', 'online_meeting_management_id')->withPivot('no_of_pc', 'times')->withTimestamps();
    }

    public function otherActivities()
    {
        return $this->belongsToMany(OtherActivitiesManagement::class, 'event_other_activities', 'event_id', 'other_activities_management_id')->withPivot('no_of_nights', 'no_of_people')->withTimestamps();
    }
}
