@props(['class' => ''])

@php
    $languages = [
        'fr' => ['name' => 'Français', 'flag' => '🇫🇷'],
        'en' => ['name' => 'English', 'flag' => '🇬🇧'],
        'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪'],
    ];
    $currentLocale = app()->getLocale();
    $currentLang = $languages[$currentLocale] ?? $languages['fr'];
@endphp

<div x-data="{ open: false }" class="relative {{ $class }}">
    <button
        @click="open = !open"
        @click.outside="open = false"
        type="button"
        class="flex items-center gap-1.5 text-sm font-medium hover:opacity-70 transition-opacity"
        style="color: var(--text-secondary);"
    >
        <span>{{ $currentLang['flag'] }}</span>
        <span class="hidden sm:inline">{{ strtoupper($currentLocale) }}</span>
        <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-40 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
        style="display: none;"
    >
        <div class="py-1">
            @foreach($languages as $code => $lang)
                <a
                    href="{{ route('language.switch', $code) }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm transition-colors {{ $currentLocale === $code ? 'bg-gray-50 font-medium' : 'hover:bg-gray-50' }}"
                    style="color: var(--text-primary);"
                >
                    <span>{{ $lang['flag'] }}</span>
                    <span>{{ $lang['name'] }}</span>
                    @if($currentLocale === $code)
                        <svg class="w-4 h-4 ml-auto text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
