<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportTranslate extends Model
{
    use HasFactory;
    protected $fillable = ['import_id', 'language_id', 'key', 'value'];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }
}
