<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    use HasFactory;

    protected $table = 'breaks';

    protected $fillable = [
        'attendance_id',
        'start_at',
        'end_at',
        'is_ended',
    ];

    public function Attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function breakModifications()
    {
        return $this->hasMany(BreakModification::class);
    }
}
