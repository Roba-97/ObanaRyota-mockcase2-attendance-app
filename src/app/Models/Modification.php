<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modification extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'modified_punch_in',
        'modified_punch_out',
        'comment',
        'application_date',
        'approval_date',
        'is_approved',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function breakModifications()
    {
        return $this->hasMany(BreakModification::class);
    }

    public function additionalBreaks()
    {
        return $this->hasOne(AdditionalBreak::class);
    }
}
