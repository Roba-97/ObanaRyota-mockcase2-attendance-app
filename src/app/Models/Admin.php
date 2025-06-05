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

        $usersWithAttendances = User::with(['attendances' => function ($query) use ($targetDate) {
            $query->whereDate('date', $targetDate);
        }])->get();

        // 出勤時刻順に並び替え
        $usersWithAttendances = $usersWithAttendances->sortBy(function ($user) {
            if ($user->attendances->isNotEmpty()) {
                return $user->attendances->first()->punch_in;
            } else {
                return Carbon::maxValue();
            }
        });

        return $usersWithAttendances->values();
    }

    public function getAllModifications($approved)
    {
        return Modification::where('is_approved', $approved)
            ->orderBy('application_date', 'asc')
            ->with('attendance')
            ->get();
    }
}
