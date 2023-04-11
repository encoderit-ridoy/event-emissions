<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineMeetingManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item',
        'parameter',
        'unit',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_online_meeting', 'online_meeting_management_id', 'event_id')->withTimestamps();
    }
}
