<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $posts=Post::with('user','category')->when(request()->search,function($posts){
            $posts=$posts->where('title','like','%' . request()->search . '%');
        })->where('user_id',auth()->user()->id)->latest()->paginate(5);

        //append query string to pagination links
        $posts->appends(['search'=>request()->search]);

        //return with api resource
        return new PostResource(true,'List data Posts', $posts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title'         => 'required|unique:posts',
            'category_id'   => 'required',
            'content'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'image'       => $image->hashName(),
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id'     => auth()->guard('api')->user()->id,
            'content'     => $request->content
        ]);


        if ($post) {
            //return success with Api Resource
            return new PostResource(true, 'Data Post Berhasil Disimpan!', $post);
        }

        //return failed with Api Resource
        return new PostResource(false, 'Data Post Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $post=Post::with('category')->whereId($id)->first();
        if($post){
            //return success with api resource
            return new PostResource(true,'Detail Data Post!',$post);
        }
        //return failed with api resource
        return new PostResource(false,'detail data post tidak ditemukan!',null);
    }

  public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:posts,title,' . $post->id,
            'category_id'   => 'required',
            'content'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        if ($request->file('image')) {

            //remove old image
            Storage::disk('local')->delete('public/posts/' . basename($post->image));

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            $post->update([
                'image'       => $image->hashName(),
                'title'       => $request->title,
                'slug'        => Str::slug($request->title, '-'),
                'category_id' => $request->category_id,
                'user_id'     => auth()->guard('api')->user()->id,
                'content'     => $request->content
            ]);
        }

        $post->update([
            'title'       => $request->title,
            'slug'        => Str::slug($request->title, '-'),
            'category_id' => $request->category_id,
            'user_id'     => auth()->guard('api')->user()->id,
            'content'     => $request->content
        ]);

        if ($post) {
            //return success with Api Resource
            return new PostResource(true, 'Data Post Berhasil Diupdate!', $post);
        }

        //return failed with Api Resource
        return new PostResource(false, 'Data Post Gagal Disupdate!', null);
    }

    // public function destroy(Post $post)
    // {
    //     Storage::disk('local')->delete('public/posts/' . basename($post->image));

    //     if($post->delete()){
    //         //return success with api resource
    //         return new PostResource(true, 'Data Post Berhasil di hapus!', null);
    //     }
    //     //return failed with Api resource
    //     return new PostResource(false,'Data Post Gagal Dihapus', null);
    // }
    public function destroy(Post $post)
{
    if ($post->image && Storage::disk('local')->exists('public/posts/' . basename($post->image))) {
        // Hapus gambar dari penyimpanan
        Storage::disk('local')->delete('public/posts/' . basename($post->image));
    }

    // Hapus post dari database
    if ($post->delete()) {
        // Berhasil dihapus
        return new PostResource(true, 'Data Post Berhasil dihapus!', null);
    } else {
        // Gagal menghapus
        return new PostResource(false, 'Data Post Gagal Dihapus', null);
    }
}


}
