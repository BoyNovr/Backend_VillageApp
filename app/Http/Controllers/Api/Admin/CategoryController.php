<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        //get categories
        $categories=Category::when(request()->search, function($categories){
            $categories=$categories->where('name','like','%' . request()->search . '%');
        })->latest()->paginate(5);

        //append query string to pagination links
        $categories->appends(['search'=>request()->search]);

        //return with api resource
        return new CategoryResource(true, 'List Data Categories', $categories);
    }

    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required|unique:categories',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        //create category
        $category=Category::create([
            'name'=>$request->name,
            'slug'=>Str::slug($request->name,'-'),
        ]);
        if($category){
        //return succes with api resource
            return new CategoryResource(true,'Data category Berhasil disimpan',$category);
        }
        return new CategoryResource(false,'Data category gagal disimpan',null);

        }

        public function show($id){
        $category=Category::whereId($id)->first();

        if($category){
            //return success with api resource
            return new CategoryResource(true,'detail data category',$category);
        }
        //return failed with api resource
        return new CategoryController(false,'Detail data category tidak ditemukan', null);
        }

         public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:categories,name,' . $category->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update category without image
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Data Category Berhasil Diupdate!', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Data Category Gagal Diupdate!', null);
    }

        public function destroy(Category $category)
        {
            if($category->delete()){
                //return success with api resource
                return new CategoryResource(true,'data category berhasil di hapus',null);
            }
            //return failed with api resource
            return new CategoryResource(false,'Data category gagal di hapus!',null);
        }

        public function all()
        {
            //return categories
            $categories=Category::latest()->get();

            //return with api resource
            return new CategoryResource(true,'List Data Categories',$categories);
        }
}
