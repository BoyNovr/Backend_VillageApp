<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;

class SliderController extends Controller
{
    public function index()
    {
        $sliders=Slider::latest()->get();
        //return with api resource
        return new SliderResource(true,'List data Sliders', $sliders);
    }
}
