<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingRoomManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meeting_room',
        'electicity_parameter',
        'other_energy_parameter',
        'unit',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_meeting_room', 'meeting_room_management_id', 'event_id')->withTimestamps();
    }
}
