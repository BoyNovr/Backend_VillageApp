<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Aparatur extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'role',
        'image',
    ];

    protected function image():Attribute
    {
        return Attribute::make(
            get:fn($image)=>url('/storage/aparaturs/' .$image),
        );
    }
}
