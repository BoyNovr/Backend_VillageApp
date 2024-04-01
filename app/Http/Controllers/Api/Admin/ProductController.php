<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use Symfony\Contracts\Service\Attribute\Required;

class ProductController extends Controller
{
    public function index()
    {
        //get products
        $products=Product::when(request()->search, function($products){
            $products=$products->where('title','like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        //append query string to pagination links
        $products->appends(['search'=>request()->search]);
    
        //return with api resource
        return new ProductResource(true, 'List Data Products', $products);
    }
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'image'=>'required|mimes:jpeg,jpg,png|max:2000',
            'title'=>'required',
            'content'=>'required',
            'owner'=>'required',
            'price'=>'required',
            'address'=>'required',
            'phone'=>'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        //upload image
        $image=$request->file('image');
        $image->storeAs('public/products',$image->hashName());

        //create product
        $product=Product::create([
            'image'=>$image->hashName(),
            'title'=>$request->title,
            'slug'=>Str::slug($request->title,'-'),
            'content'=>$request->content,
            'owner'=>$request->owner,
            'price'=>$request->price,
            'address'=>$request->address,
            'phone'=>$request->phone,
            'user_id'=>auth()->guard('api')->user()->id,
        ]);
        if($product){
            //return success with api resource
            return new ProductResource(true,'Data product berhasil disimpan',$product);
        }
        //return failed with api resource
        return new ProductResource(false,'Data product gagal disimpan',null);
    }

    public function show($id)
    {
        $product=Product::whereId($id)->first();
        if ($product){
            //return success with api resource
            return new ProductResource(true,'Detail Data product',$product);
        }
        //return failed with api resource
        return new ProductResource(false,'Detail data product tidak ditemukan', null);
    }

    public function update(Request $request, Product $product)
    {
        $validator=Validator::make($request->all(),[
            'title'=>'required',
            'content'=>'required',
            'owner'=>'required',
            'price'=>'required',
            'address'=>'required',
            'phone'=>'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        //check image update
        if($request->file('image')){

            //remove old image
            Storage::disk('local')->delete('public/products/' . basename($product->image));
            
            //upload new image
            $image=$request->file('image');
            $image->storeAs('public/products', $image->hashName());

            //update Product with new image
            $product->update([
                'image'=>$image->hashName(),
                'title'=>$request->title,
                'slug'=>Str::slug($request->title,'-'),
                'content'=>$request->content,
                'owner'=>$request->owner,
                'price'=>$request->price,
                'address'=>$request->address,
                'phone'=>$request->phone,
                'user_id'=>auth()->guard('api')->user()->id,
            ]);
        }

        //update product without image
        $product->update([
            'title'=>$request->title,
            'slug'=>Str::slug($request->title,'-'),
            'content'=>$request->content,
            'owner'=>$request->owner,
            'price'=>$request->price,
            'address'=>$request->address,
            'phone'=>$request->phone,
            'user_id'=>auth()->guard('api')->user()->id,
        ]);

        if($product){
            //return success with api Resource
            return new ProductResource(true, 'Data Product Berhasil di Update!', $product);
        }
        //return failed with api resource
        return new ProductResource(false,'Data Product Gagal di update', null);
    }

    public function destroy(Product $product)
    {
        Storage::disk('local')->delete('public/products/' . basename($product->image));

        if($product->delete()){
            //return success with api resource
            return new ProductResource(true, 'data Product berhasil di hapus', null);
        }
        //return failed with api resource
        return new ProductResource(false,'Data Product Gagal di hapus',null);
    }
}
