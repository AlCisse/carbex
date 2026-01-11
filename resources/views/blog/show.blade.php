@extends('layouts.marketing')

@section('title', ($post->meta_title ?? $post->title) . ' - Blog Carbex')
@section('description', $post->meta_description ?? $post->excerpt ?? Str::limit(strip_tags($post->content), 160))

@section('content')
<article class="pt-32 pb-20">
    <!-- Header -->
    <header class="max-w-3xl mx-auto px-6 mb-12">
        <div class="mb-8">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-sm transition-colors hover:opacity-80" style="color: var(--accent);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Retour au blog
            </a>
        </div>

        <div class="flex items-center gap-3 mb-6">
            <span class="text-sm" style="color: var(--text-muted);">{{ $post->published_at->format('d M Y') }}</span>
            <span class="text-sm" style="color: var(--text-muted);">·</span>
            <span class="text-sm" style="color: var(--text-muted);">{{ $post->reading_time }} min de lecture</span>
        </div>

        <h1 class="text-4xl md:text-5xl font-semibold mb-8" style="color: var(--text-primary); letter-spacing: -0.025em; line-height: 1.2;">
            {{ $post->title }}
        </h1>

        @if($post->excerpt)
        <p class="text-xl" style="color: var(--text-secondary);">
            {{ $post->excerpt }}
        </p>
        @endif

        <div class="flex items-center gap-4 mt-8 pt-8 border-t" style="border-color: var(--border);">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-medium text-white" style="background-color: var(--accent);">
                {{ substr($post->author->name ?? 'C', 0, 1) }}
            </div>
            <div>
                <p class="font-medium" style="color: var(--text-primary);">{{ $post->author->name ?? 'Carbex' }}</p>
                <p class="text-sm" style="color: var(--text-muted);">{{ $post->author->email ?? 'contact@carbex.fr' }}</p>
            </div>
        </div>
    </header>

    <!-- Featured Image -->
    @if($post->featured_image)
    <div class="max-w-4xl mx-auto px-6 mb-12">
        <div class="aspect-video rounded-2xl overflow-hidden">
            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </div>
    </div>
    @endif

    <!-- Content -->
    <div class="max-w-3xl mx-auto px-6">
        <div class="prose prose-lg max-w-none" style="color: var(--text-secondary);">
            {!! $post->content !!}
        </div>

        <!-- Share -->
        <div class="mt-12 pt-8 border-t" style="border-color: var(--border);">
            <p class="text-sm font-medium mb-4" style="color: var(--text-primary);">Partager cet article</p>
            <div class="flex items-center gap-3">
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(route('blog.show', $post->slug)) }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors" style="background-color: var(--accent-light); color: var(--accent);" aria-label="Partager sur Twitter">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('blog.show', $post->slug)) }}&title={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors" style="background-color: var(--accent-light); color: var(--accent);" aria-label="Partager sur LinkedIn">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </a>
                <button onclick="navigator.clipboard.writeText('{{ route('blog.show', $post->slug) }}'); this.innerHTML='<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'/></svg>'; setTimeout(() => this.innerHTML='<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\'/></svg>', 2000)" class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors" style="background-color: var(--accent-light); color: var(--accent);" aria-label="Copier le lien">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</article>

<!-- Related Posts -->
@if($relatedPosts->count() > 0)
<section class="py-20" style="background-color: #f8fafc;">
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-2xl font-semibold mb-8" style="color: var(--text-primary);">Articles similaires</h2>
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($relatedPosts as $related)
            <a href="{{ route('blog.show', $related->slug) }}" class="group">
                <article class="h-full rounded-xl border overflow-hidden transition-all duration-200 hover:shadow-lg" style="border-color: var(--border); background: white;">
                    <div class="aspect-video overflow-hidden" style="background-color: var(--accent-light);">
                        @if($related->featured_image)
                        <img src="{{ $related->featured_image_url }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <span class="text-sm" style="color: var(--text-muted);">{{ $related->published_at->format('d M Y') }}</span>
                        <h3 class="text-lg font-semibold mt-2 group-hover:text-teal-600 transition-colors line-clamp-2" style="color: var(--text-primary);">
                            {{ $related->title }}
                        </h3>
                    </div>
                </article>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA -->
<section class="py-20" style="background-color: var(--accent);">
    <div class="max-w-3xl mx-auto px-6 text-center">
        <h2 class="text-2xl font-semibold text-white mb-4">
            Pret a mesurer votre empreinte carbone ?
        </h2>
        <p class="text-white/80 mb-8">
            Commencez votre bilan carbone en quelques minutes avec Carbex.
        </p>
        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white rounded-lg text-sm font-medium transition-colors hover:bg-gray-100" style="color: var(--accent);">
            Demarrer l'essai gratuit
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</section>
@endsection
