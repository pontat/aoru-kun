<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class LineUser extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['line_id', 'display_name', 'picture_url'];
}
