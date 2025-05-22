<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAttendancesByDate($date)
    {
        $targetDate = Carbon::parse($date)->format('Y-m-d');
        return Attendance::whereDate('date', $targetDate)->with('breaks', 'user')->get();
    }

    public function getAllModifications($approved)
    {
        return Modification::where('is_approved', $approved)->with('attendance')->get();
    }
}
