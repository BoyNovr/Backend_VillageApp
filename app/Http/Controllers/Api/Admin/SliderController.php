<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        //get data sliders
        $sliders=Slider::latest()->paginate(5);

        //return with api resource
        return new SliderResource(true, 'List Data Sliders', $sliders);
    }

    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'image'=>'required|image|mimes:jpeg,jpg,png|max:2000',
        ]);

        //UPLOAD image
        $image=$request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        $slider=Slider::create([
            'image' => $image->hashName(),
        ]);

        if($slider){
            //return success with api resource
            return new SliderResource(true,'Data Slider Berhasil disimpan', $slider);
        }
        //return failed with api resource
        return new SliderResource(false,'Data Slider Gagal disimpan!', null);
    }

    public function destroy(Slider $slider)
    {
        //remove image
        Storage::disk('local')->delete('public/sliders/' .basename($slider->image));

        if($slider->delete()){
            //return success with api resource
            return new SliderResource(true,'Data Slider Berhasil dihapus', null);
        }
        //return failed with api resource
        return new SliderResource(false,'Data Slider Gagal Dihapus!', null);
        
    }
}
