<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * Generate dynamic sitemap.xml
     */
    public function sitemap(): Response
    {
        $urls = collect();

        // Static pages
        $staticPages = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => route('pricing'), 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => route('pour-qui'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => route('contact'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => route('blog.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => route('cgv'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['url' => route('cgu'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['url' => route('mentions-legales'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['url' => route('engagements'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => route('login'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['url' => route('register'), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $urls->push($page);
        }

        // Blog posts
        $blogPosts = BlogPost::published()
            ->select(['slug', 'updated_at'])
            ->orderBy('published_at', 'desc')
            ->get();

        foreach ($blogPosts as $post) {
            $urls->push([
                'url' => route('blog.show', $post->slug),
                'lastmod' => $post->updated_at->toW3cString(),
                'priority' => '0.6',
                'changefreq' => 'monthly',
            ]);
        }

        $content = view('seo.sitemap', ['urls' => $urls])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate robots.txt
     */
    public function robots(): Response
    {
        $content = view('seo.robots')->render();

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
