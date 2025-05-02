<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'punch_in',
        'punch_out',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function modification()
    {
        return $this->hasOne(Modification::class);
    }


    public function calculateTotalBreakMinutes()
    {
        return $this->breaks->sum(function ($break) {
            if ($break->start_at && $break->end_at) {
                return Carbon::parse($break->end_at)
                    ->diffInMinutes(Carbon::parse($break->start_at));
            }
            return 0;
        });
    }

    public function getBreakDurationAttribute()
    {
        $minutes = $this->calculateTotalBreakMinutes();
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return sprintf('%d:%02d', $hours, $mins);
    }

    public function calculateTotalWorkMinutes()
    {
        if (!$this->punch_in || !$this->punch_out) {
            return 0;
        }

        $total = Carbon::parse($this->punch_in)->diffInMinutes(Carbon::parse($this->punch_out));

        return $total - $this->calculateTotalBreakMinutes();
    }

    public function getWorkDurationAttribute()
    {
        $minutes = $this->calculateTotalWorkMinutes();
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return sprintf('%d:%02d', $hours, $mins);
    }
}
