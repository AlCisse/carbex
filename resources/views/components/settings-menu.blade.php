@props(['active' => null])

<nav class="bg-white rounded-lg shadow-sm border border-gray-200 p-2 space-y-1">
    <a href="{{ route('settings') }}"
       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $active === 'organization' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 {{ $active === 'organization' ? 'text-green-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        {{ __('linscarbon.settings.my_company') }}
    </a>

    <a href="{{ route('settings.team') }}"
       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $active === 'team' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 {{ $active === 'team' ? 'text-green-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        {{ __('linscarbon.settings.users') }}
    </a>

    <a href="{{ route('settings.profile') }}"
       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $active === 'profile' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 {{ $active === 'profile' ? 'text-green-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        {{ __('linscarbon.settings.profile') }}
    </a>

    <a href="{{ route('settings.sites') }}"
       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $active === 'sites' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 {{ $active === 'sites' ? 'text-green-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        {{ __('linscarbon.settings.sites') }}
    </a>

    <a href="{{ route('settings.ai') }}"
       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $active === 'ai' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 {{ $active === 'ai' ? 'text-green-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
        {{ __('linscarbon.settings.ai.title') }}
        <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
            NEW
        </span>
    </a>

    <a href="{{ route('billing') }}"
       class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $active === 'billing' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <svg class="mr-3 h-5 w-5 {{ $active === 'billing' ? 'text-green-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
        {{ __('linscarbon.settings.billing') }}
    </a>
</nav>
