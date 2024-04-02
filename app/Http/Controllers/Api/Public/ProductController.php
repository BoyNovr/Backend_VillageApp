<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products=Product::latest()->paginate(9);

        //return with api resource
        return new ProductResource(true, 'List Data Product', $products);
    }

    public function show($slug)
    {
        $product=Product::where('slug', $slug)->first();

        if($product){
            //return with api resource
            return new ProductResource(true,'Detail Data Product', $product);
        }
        //return with api resource
        return new ProductResource(false,'Detail Data Product Tidak ditemukan!', null);
    }

    public function homePage()
    {
        $products=Product::latest()->take(6)->get();
        //return with api resource
        return new ProductResource(true, 'List Data Products HomePage', $products);
    }
}
