<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\AparaturResource;
use App\Models\Aparatur;

class AparaturController extends Controller
{
    public function index()
    {
        $aparatur=Aparatur::oldest()->get();
        //return with api resource
        return new AparaturResource(true,'List Data Aparaturs', $aparatur);
    }
}
