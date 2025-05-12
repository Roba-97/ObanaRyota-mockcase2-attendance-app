<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'modification_id',
        'added_start_at',
        'added_end_at',
    ];

    public function modification()
    {
        return $this->belongsTo(Modification::class);
    }
}
