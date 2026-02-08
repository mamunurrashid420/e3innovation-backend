<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [];

    protected $casts = [
        'technologies' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
    ];
}
