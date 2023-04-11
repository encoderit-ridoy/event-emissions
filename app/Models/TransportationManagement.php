<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportationManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transporation_way',
        'parameter',
        'unit',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_transportation', 'transportation_management_id', 'event_id')->withTimestamps();
    }
}
