<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'kategori';
    protected $guarded = [];

    public function division()
    {
        return $this->belongsTo('App\Models\Division', 'division_code', 'code');
    }
}
