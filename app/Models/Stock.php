<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'stock';
    protected $guarded = [];

    public function material()
    {
        return $this->belongsTo('App\Models\Material', 'id_material', 'id');
    }
}
