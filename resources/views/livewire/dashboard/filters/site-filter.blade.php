<div>
    <label for="site-filter" class="sr-only">{{ __('carbex.dashboard.filter_by_site') }}</label>
    <select
        id="site-filter"
        wire:model.live="selectedSite"
        class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 text-sm focus:ring-green-500 focus:border-green-500"
    >
        <option value="">{{ __('carbex.dashboard.all_sites') }}</option>
        @foreach($this->sites as $site)
            <option value="{{ $site->id }}">
                {{ $site->name }}
                @if($site->city)
                    ({{ $site->city }})
                @endif
            </option>
        @endforeach
    </select>
</div>
