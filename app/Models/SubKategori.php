<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubKategori extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sub_kategori';
    protected $guarded = [];

    public function kategori()
    {
        return $this->belongsTo('App\Models\Kategori', 'id_kategori', 'id');
    }
}
