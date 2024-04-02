<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts=Post::with('user','category')->latest()->paginate(10);

        //return with Api resource
        return new PostResource(true,'List data Posts', $posts);
    }

    public function show($slug)
    {
        $post=Post::with('user','category')->where('slug',$slug)->first();
        if($post){
            //return with api resource
            return new PostResource(true,'Detail Data Post', $post);
        }
        //return with api resource
        return new PostResource(false,'Detail Data Post Tidak ditemukan!', null);
    }

    public function homePage()
    {
        $posts=Post::with('user','category')->latest()->take(6)->get();
        //return with api resource
        return new PostResource(true,'List data Post HomePage', $posts);
    }
}
