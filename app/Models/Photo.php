<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable=[
        'image',
        'caption',
    ];

    protected function image():Attribute
    {
        return Attribute::make(
            get:fn($image)=>url('/storage/photos/' .$image),
        );
    }
}
