<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use App\http\Resources\PhotoResource;
use Illuminate\Support\Facades\Validator;

class PhotoController extends Controller
{
    //get photos
    public function index()
    {
    $photos=Photo::when(request()->search, function ($photos){
        $photos=$photos->where('caption', 'like', '%' . request()->search . '%');
    })->latest()->paginate(5);

    //append query string to pagination links
    $photos->appends(['search'=>request()->search]);

    //return with api resource
    return new PhotoResource(true, 'List Data Photos' , $photos);

    }

    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'image' => 'required|mimes:jpeg,jpg,png|max:2000',
            'caption' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        //upload image
        $image= $request->file('image');
        $image->storeAs('public/photos', $image->hashName());

        //create Photo
        $photo =Photo::create([
            'image'=> $image->hashName(),
            'caption'=>$request->caption,
        ]);

        if($photo){
            //return succes with api resource
            return new PhotoResource(true, 'data photo berhasil disimpan', $photo);
        }
        //return failed with api resource
        return new PhotoResource(false, 'Data Photo gagal disimpan', null);
    }

    public function destroy(Photo $photo)
    {
        //remove image
        Storage::disk('local')->delete('public/photos/' . basename($photo->image));

        if($photo->delete()){
            //return success with api resource
            return new PhotoResource(true, 'Data Photo Berhasil Dihapus!', null);
        }
        //return failed with api resource
        return new PhotoResource(false,'Data Photo gagal dihapus!', null);
    }
}
