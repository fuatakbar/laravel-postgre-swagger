<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventImage extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
      'event_id', 'path'
    ];

    public function event() {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }
}
