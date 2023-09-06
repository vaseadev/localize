<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function languages()
    {
        return $this->hasMany(Language::class);
    }

    public function tokens()
    {
        return $this->hasMany(ProjectToken::class);
    }

    public function translates()
    {
        return $this->hasMany(Translate::class);
    }

    public function imports()
    {
        return $this->hasMany(Import::class);
    }

    public function access()
    {
        return $this->belongsToMany(User::class);
    }
}
