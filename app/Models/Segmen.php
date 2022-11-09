<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Segmen extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'segmen';
    protected $guarded = [];

    public function subKategori()
    {
        return $this->belongsTo('App\Models\SubKategori', 'id_sub_kategori', 'id');
    }
}
