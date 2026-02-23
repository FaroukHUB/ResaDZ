<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $featuredPost = BlogPost::published()
            ->featured()
            ->orderBy('published_at', 'desc')
            ->first();

        return view('front.pages.blog', compact('posts', 'featuredPost'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $post->increment('views_count');

        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->when($post->category, fn ($q) => $q->where('category', $post->category))
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('front.pages.blog-show', compact('post', 'relatedPosts'));
    }
}
