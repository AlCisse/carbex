<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('linscarbon.promote.title') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('linscarbon.promote.subtitle') }}
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-2 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-2 rounded-lg">
            <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span class="font-semibold text-emerald-700 dark:text-emerald-300">
                {{ $this->totalPoints }} {{ __('linscarbon.promote.points') }}
            </span>
        </div>
    </div>

    @if($this->earnedBadges->isEmpty())
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                {{ __('linscarbon.promote.no_badges') }}
            </h3>
            <p class="mt-2 text-gray-500 dark:text-gray-400">
                {{ __('linscarbon.promote.no_badges_hint') }}
            </p>
            <a href="{{ route('dashboard') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium">
                {{ __('linscarbon.promote.start_assessment') }}
            </a>
        </div>
    @else
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Badge Gallery (Left) -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('linscarbon.promote.your_badges') }}
                    </h2>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($this->earnedBadges as $badge)
                            <button
                                wire:click="selectBadge('{{ $badge['id'] }}')"
                                class="p-4 rounded-lg border-2 transition-all {{ $this->selectedBadge && $this->selectedBadge['id'] === $badge['id'] ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-emerald-300' }}"
                            >
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full {{ $badge['color_class'] }} flex items-center justify-center mb-2">
                                        @if($badge['icon'])
                                            <span class="text-2xl">{{ $badge['icon'] }}</span>
                                        @else
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center line-clamp-2">
                                        {{ $badge['name'] }}
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Selected Badge Preview (Center/Right) -->
            <div class="lg:col-span-2">
                @if($this->selectedBadge)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <!-- Badge Preview -->
                        <div class="flex flex-col md:flex-row items-start gap-6">
                            <!-- Badge Visual -->
                            <div class="flex-shrink-0">
                                <div class="w-32 h-40 rounded-lg {{ $this->selectedBadge['color_class'] }} flex flex-col items-center justify-center p-4 shadow-lg">
                                    <div class="w-16 h-16 bg-white/90 rounded-full flex items-center justify-center mb-3">
                                        @if($this->selectedBadge['icon'])
                                            <span class="text-4xl">{{ $this->selectedBadge['icon'] }}</span>
                                        @else
                                            <svg class="w-10 h-10 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-white/90 text-center">CARBEX</span>
                                </div>
                            </div>

                            <!-- Badge Info -->
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $this->selectedBadge['name'] }}
                                </h3>
                                <p class="mt-2 text-gray-600 dark:text-gray-400">
                                    {{ $this->selectedBadge['description'] }}
                                </p>
                                <div class="mt-4 flex flex-wrap gap-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ $this->selectedBadge['points'] }} pts
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                        {{ __('linscarbon.promote.earned') }} {{ \Carbon\Carbon::parse($this->selectedBadge['earned_at'])->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                {{ __('linscarbon.promote.share_badge') }}
                            </h4>
                            <div class="flex flex-wrap gap-3">
                                <!-- Share on LinkedIn -->
                                <a href="{{ $this->getLinkedInShareUrl() }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center px-4 py-2 bg-[#0077b5] hover:bg-[#006399] text-white rounded-lg font-medium text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                    LinkedIn
                                </a>

                                <!-- Share on Twitter/X -->
                                <a href="{{ $this->getTwitterShareUrl() }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center px-4 py-2 bg-black hover:bg-gray-800 text-white rounded-lg font-medium text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                    X (Twitter)
                                </a>

                                <!-- Copy Link -->
                                <button
                                    x-data="{ copied: false }"
                                    x-on:click="navigator.clipboard.writeText('{{ $this->getShareUrl() }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium text-sm"
                                >
                                    <svg x-show="!copied" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                    </svg>
                                    <svg x-show="copied" class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span x-text="copied ? '{{ __('linscarbon.promote.copied') }}' : '{{ __('linscarbon.promote.copy_link') }}'"></span>
                                </button>

                                <!-- Embed Code -->
                                <button
                                    wire:click="openEmbedModal"
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium text-sm"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                    </svg>
                                    {{ __('linscarbon.promote.embed') }}
                                </button>
                            </div>
                        </div>

                        <!-- Download Section -->
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                                {{ __('linscarbon.promote.download_assets') }}
                            </h4>
                            <div class="flex flex-wrap gap-3">
                                <button
                                    wire:click="openDownloadModal"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium text-sm"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    {{ __('linscarbon.promote.download_badge') }}
                                </button>

                                <button
                                    wire:click="downloadEmailSignature"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium text-sm"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ __('linscarbon.promote.email_signature') }}
                                </button>

                                <button
                                    wire:click="downloadSocialKit"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium text-sm"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ __('linscarbon.promote.social_kit') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Embed Modal -->
    @if($showEmbedModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeEmbedModal"></div>

                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6 z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('linscarbon.promote.embed_badge') }}
                        </h3>
                        <button wire:click="closeEmbedModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Size Options -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('linscarbon.promote.embed_size') }}
                        </label>
                        <div class="flex gap-2">
                            @foreach(['small' => 'S', 'medium' => 'M', 'large' => 'L'] as $size => $label)
                                <button
                                    wire:click="setEmbedSize('{{ $size }}')"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $embedSize === $size ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}"
                                >
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Embed Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('linscarbon.promote.embed_code') }}
                        </label>
                        <div class="relative">
                            <textarea
                                readonly
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-mono text-xs"
                            >{{ $this->embedCode }}</textarea>
                            <button
                                x-data="{ copied: false }"
                                x-on:click="navigator.clipboard.writeText(`{{ $this->embedCode }}`); copied = true; setTimeout(() => copied = false, 2000)"
                                class="absolute top-2 right-2 p-1 text-gray-400 hover:text-emerald-500"
                            >
                                <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                </svg>
                                <svg x-show="copied" class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Download Modal -->
    @if($showDownloadModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeDownloadModal"></div>

                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6 z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('linscarbon.promote.download_badge') }}
                        </h3>
                        <button wire:click="closeDownloadModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Format Options -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('linscarbon.promote.format') }}
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach(['png' => 'PNG', 'svg' => 'SVG', 'pdf' => 'PDF'] as $format => $label)
                                <button
                                    wire:click="setDownloadFormat('{{ $format }}')"
                                    class="px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ $downloadFormat === $format ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}"
                                >
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <button
                        wire:click="downloadBadge"
                        class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium"
                    >
                        {{ __('linscarbon.promote.download') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('download-file', (data) => {
        const link = document.createElement('a');
        link.href = data[0].url;
        link.download = data[0].filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    $wire.on('download-html', (data) => {
        const blob = new Blob([data[0].content], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = data[0].filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    });
</script>
@endscript
