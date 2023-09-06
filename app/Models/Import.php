<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'project_id', 'type'];

    public function importTranslates()
    {
        return $this->hasMany(ImportTranslate::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
