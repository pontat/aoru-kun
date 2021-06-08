<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $casts = [
        'is_completed' => 'boolean'
    ];

    protected $fillable = ['line_user_id', 'name', 'is_completed'];

    public function scopeLineUserIncompleteTargetDate($query, LineUser $lineUser, Carbon $date)
    {
        return $query->where('line_user_id', $lineUser->id)
            ->where('is_completed', false)
            ->whereDate('created_at', $date);
    }
}
