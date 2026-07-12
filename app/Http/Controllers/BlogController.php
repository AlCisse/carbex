<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Display blog index with published posts.
     */
    public function index(): View
    {
        $featuredPost = BlogPost::published()
            ->latest('published_at')
            ->first();

        $posts = BlogPost::published()
            ->when($featuredPost, fn ($query) => $query->where('id', '!=', $featuredPost->id))
            ->latest('published_at')
            ->paginate(9);

        return view('blog.index', [
            'featuredPost' => $featuredPost,
            'posts' => $posts,
        ]);
    }

    /**
     * Display a single blog post.
     */
    public function show(string $slug): View
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
