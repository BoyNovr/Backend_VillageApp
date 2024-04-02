<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoResource;
use App\Models\Photo;

class PhotoController extends Controller
{
    public function index()
    {
        $photos=Photo::latest()->paginate(9);

        //return with api resource
        return new PhotoResource(true,'List Data Photos', $photos);
    }
}
