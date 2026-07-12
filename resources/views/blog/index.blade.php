@extends('layouts.marketing')

@section('title', 'Blog - LinsCarbon')
@section('description', 'Actualites, conseils et guides sur le bilan carbone, la strategie climat et la decarbonation des entreprises.')

@section('content')
<section class="pt-32 pb-20" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-16">
            <p class="text-sm font-medium mb-4" style="color: var(--accent);">Blog</p>
            <h1 class="text-4xl font-semibold mb-6" style="color: var(--text-primary); letter-spacing: -0.025em;">
                Ressources et actualites climat
            </h1>
            <p class="text-lg max-w-2xl mx-auto" style="color: var(--text-secondary);">
                Guides pratiques, analyses et conseils pour piloter votre strategie carbone.
            </p>
        </div>

        @if($featuredPost)
        <!-- Featured Post -->
        <a href="{{ route('blog.show', $featuredPost->slug) }}" class="block mb-16 group">
            <article class="grid md:grid-cols-2 gap-8 p-6 rounded-2xl border transition-all duration-200 hover:shadow-lg" style="border-color: var(--border); background: white;">
                <div class="aspect-video rounded-xl overflow-hidden" style="background-color: var(--accent-light);">
                    @if($featuredPost->featured_image)
                    <img src="{{ $featuredPost->featured_image_url }}" alt="{{ $featuredPost->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-16 h-16" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 text-xs font-medium rounded-full" style="background-color: var(--accent-light); color: var(--accent);">A la une</span>
                        <span class="text-sm" style="color: var(--text-muted);">{{ $featuredPost->published_at->format('d M Y') }}</span>
                    </div>
                    <h2 class="text-2xl font-semibold mb-4 group-hover:text-teal-600 transition-colors" style="color: var(--text-primary);">
                        {{ $featuredPost->title }}
                    </h2>
                    <p class="mb-6" style="color: var(--text-secondary);">
                        {{ $featuredPost->excerpt ?? Str::limit(strip_tags($featuredPost->content), 200) }}
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium text-white" style="background-color: var(--accent);">
                                {{ substr($featuredPost->author->name ?? 'C', 0, 1) }}
                            </div>
                            <span class="text-sm" style="color: var(--text-secondary);">{{ $featuredPost->author->name ?? 'LinsCarbon' }}</span>
                        </div>
                        <span class="text-sm" style="color: var(--text-muted);">{{ $featuredPost->reading_time }} min de lecture</span>
                    </div>
                </div>
            </article>
        </a>
        @endif

        <!-- Posts Grid -->
        @if($posts->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="group">
                <article class="h-full rounded-xl border overflow-hidden transition-all duration-200 hover:shadow-lg" style="border-color: var(--border); background: white;">
                    <div class="aspect-video overflow-hidden" style="background-color: var(--accent-light);">
                        @if($post->featured_image)
                        <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-sm" style="color: var(--text-muted);">{{ $post->published_at->format('d M Y') }}</span>
                            <span class="text-sm" style="color: var(--text-muted);">·</span>
                            <span class="text-sm" style="color: var(--text-muted);">{{ $post->reading_time }} min</span>
                        </div>
                        <h3 class="text-lg font-semibold mb-3 group-hover:text-teal-600 transition-colors line-clamp-2" style="color: var(--text-primary);">
                            {{ $post->title }}
                        </h3>
                        <p class="text-sm line-clamp-3" style="color: var(--text-secondary);">
                            {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 120) }}
                        </p>
                    </div>
                </article>
            </a>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
        <div class="mt-12">
            {{ $posts->links() }}
        </div>
        @endif
        @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto mb-4" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <p class="text-lg" style="color: var(--text-secondary);">Aucun article pour le moment.</p>
            <p class="text-sm mt-2" style="color: var(--text-muted);">Revenez bientot pour decouvrir nos contenus.</p>
        </div>
        @endif
    </div>
</section>

<!-- Newsletter CTA -->
<section class="py-20" style="background-color: var(--accent);">
    <div class="max-w-3xl mx-auto px-6 text-center">
        <h2 class="text-2xl font-semibold text-white mb-4">
            Restez informe des actualites climat
        </h2>
        <p class="text-white/80 mb-8">
            Recevez nos derniers articles et guides directement dans votre boite mail.
        </p>
        <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
            <input type="email" placeholder="Votre email" required
                   class="flex-1 px-4 py-3 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-white/50">
            <button type="submit" class="px-6 py-3 bg-white rounded-lg text-sm font-medium transition-colors hover:bg-gray-100" style="color: var(--accent);">
                S'abonner
            </button>
        </form>
    </div>
</section>
@endsection
