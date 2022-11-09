<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubSegmen extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sub_segmen';
    protected $guarded = [];

    public function segmen()
    {
        return $this->belongsTo('App\Models\Segmen', 'id_segmen', 'id');
    }
}
