<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'material';
    protected $guarded = [];

    public function subSegmen()
    {
        return $this->belongsTo('App\Models\SugSegmen', 'id_sub_segmen', 'id');
    }

    public function stock()
    {
        return $this->hasMany('App\Models\Stock', 'id_material', 'id');
    }
}
