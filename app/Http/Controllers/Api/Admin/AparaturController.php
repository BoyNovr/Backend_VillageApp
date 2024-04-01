<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AparaturResource;
use App\Models\Aparatur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AparaturController extends Controller
{
    public function index()
    {
        $aparaturs=Aparatur::when(request()->search, function ($aparaturs){
            $aparaturs=$aparaturs->where('name', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        //append query string to pagination links
        $aparaturs->appends(['search'=>request()->search]);

        //return with api resource
        return new AparaturResource(true, 'List Data Aparaturs', $aparaturs);
    }

    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'image' => 'required|mimes:jpeg,jpg,png|max:2000',
            'name' =>'required',
            'role' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        //upload image
        $image=$request->file('image');
        $image->storeAs('public/aparaturs', $image->hashName());

        //create aparatur
        $aparatur= Aparatur::create([
            'image'=>$image->hashName(),
            'name' =>$request->name,
            'role' =>$request->role,
        ]);

        if($aparatur){
            //return success with api resource
            return new AparaturResource(true,'Data Aparatur Berhasil disimpan', $aparatur);
        }
        //return failed with api resource
        return new AparaturResource(false, 'Data Aparatur gagal disimpan',null);
    }

    public function show($id)
    {
        $aparatur=Aparatur::whereId($id)->first();
        if($aparatur){
            //return success with api resource
            return new AparaturResource(true, 'Detail Data Aparatur!', $aparatur);
        }
        //return failed with api resource
        return new AparaturResource(false,'detail data aparatur tidak ditemukan', null);
    }

    public function update(Request $request, Aparatur $aparatur)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'role'=>'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        //check image update
        if($request->file('image')){
            //remove old image
            Storage::disk('local')->delete('public/aparaturs/' . basename($aparatur->image));

            //upload new image
            $image=$request->file('image');
            $image->storeAs('public/aparaturs', $image->hashName());

            //update aparatur with new image
            $aparatur->update([
                'image'=>$image->hashName(),
                'name' =>$request->name,
                'role' => $request->role,
            ]);
        }
        //update aparatur without image
        $aparatur->update([
            'name'=>$request->name,
            'role'=>$request->role,
        ]);
        if($aparatur){
            //return success with api resource
            return new AparaturResource(true, 'Data Aparatur berhasil di update', $aparatur);
        }
        //return failed with api resource
        return new AparaturResource(false,'data Aparatur gagal diupdate', null);
    }

    public function destroy(Aparatur $aparatur)
    {
        //remove image
        Storage::disk('local')->delete('public/aparaturs/' . basename($aparatur->image));

        if($aparatur->delete()){
            //return success with api resource
            return new AparaturResource(true, 'Data Aparatur Berhasil dihapus!', null);
        }
        //return failed with api resource
        return new AparaturResource(false,'data Aparatur gagal dihapus!', null);
    }
}
