<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectToken extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'token', 'name', 'scope'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
