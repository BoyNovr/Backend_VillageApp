<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $pages=Page::oldest()->get();
        //return with api resource
        return new PageResource(true,'List data Pages', $pages);
    }
    public function show($slug)
    {
         $page = Page::where('slug', $slug)->first();
        if($page){
            //return with api resource
            return new PageResource(true, 'Detail data Page', $page);
        }
        return new PageResource(false,'Detail data Page Tidak ditemukan', null);
    }
}
