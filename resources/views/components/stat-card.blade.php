@props([
    'title' => '',
    'value' => '',
    'change' => null,
    'changeType' => 'neutral', // positive, negative, neutral
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-lg']) }}>
    <div class="p-5">
        <div class="flex items-center">
            @if($icon)
            <div class="flex-shrink-0">
                <div class="rounded-md bg-green-50 p-3">
                    {!! $icon !!}
                </div>
            </div>
            @endif
            <div class="{{ $icon ? 'ml-5' : '' }} w-0 flex-1">
                <dl>
                    <dt class="truncate text-sm font-medium text-gray-500">
                        {{ $title }}
                    </dt>
                    <dd class="mt-1">
                        <div class="text-2xl font-semibold text-gray-900">
                            {{ $value }}
                        </div>
                    </dd>
                </dl>
            </div>
        </div>
        @if($change !== null)
        <div class="mt-4">
            <div class="flex items-center text-sm">
                @if($changeType === 'positive')
                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-green-600 font-medium">{{ $change }}</span>
                @elseif($changeType === 'negative')
                <svg class="h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-red-600 font-medium">{{ $change }}</span>
                @else
                <span class="text-gray-500">{{ $change }}</span>
                @endif
                <span class="ml-2 text-gray-500">{{ __('carbex.common.vs_previous_period') }}</span>
            </div>
        </div>
        @endif
    </div>
</div>
