<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakModification extends Model
{
    use HasFactory;

    protected $fillable = [
        'modification_id',
        'break_id',
        'modified_start_at',
        'modified_end_at',
    ];

    public function modification()
    {
        return $this->belongsTo(Modification::class);
    }

    public function modificationTarget()
    {
        return $this->belongsTo(BreakTime::class);
    }
}
