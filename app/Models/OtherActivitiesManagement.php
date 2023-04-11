<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherActivitiesManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meeting_room',
        'parameter',
        'unit',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_other_activities', 'other_activities_management_id', 'event_id')->withTimestamps();
    }
}
