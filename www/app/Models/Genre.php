<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Genre extends Model
{
    use SoftDeletes, Uuid;

    public $incrementing = false; // necessario quando usar UUID 
    protected $keyType = 'string'; // necessario quando usar UUID 
    //protected $guarded = [] ; // estrategia para não precisar descrever cada campo no $fillable
    protected $fillable = ['name', 'is_active'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'is_active' => 'boolean'
    ];
}


