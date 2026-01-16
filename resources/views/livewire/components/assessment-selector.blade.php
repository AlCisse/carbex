<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" type="button" class="flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
        <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        @if($activeAssessment)
            <span>{{ $activeAssessment->year }}</span>
            @if($activeAssessment->isActive())
                <span class="ml-1.5 inline-flex h-2 w-2 rounded-full bg-green-500"></span>
            @endif
        @else
            <span class="text-gray-400">{{ __('carbex.assessments.none') }}</span>
        @endif
        <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
        <div class="py-1">
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                {{ __('carbex.assessments.title') }}
            </div>

            @forelse($assessments as $assessment)
                <button wire:click="switchAssessment({{ $assessment->year }})"
                        @click="open = false"
                        class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $activeAssessment?->id === $assessment->id ? 'bg-green-50 text-green-700' : '' }}">
                    <span class="flex items-center">
                        {{ __('carbex.assessments.year_label', ['year' => $assessment->year]) }}
                        @if($assessment->isActive())
                            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                {{ __('carbex.assessments.status_active') }}
                            </span>
                        @elseif($assessment->isCompleted())
                            <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ __('carbex.assessments.status_completed') }}
                            </span>
                        @endif
                    </span>
                    <span class="text-xs text-gray-400">{{ $assessment->completion_percent }}%</span>
                </button>
            @empty
                <div class="px-4 py-3 text-sm text-gray-500 text-center">
                    {{ __('carbex.assessments.empty_short') }}
                </div>
            @endforelse

            <div class="border-t border-gray-100 mt-1 pt-1">
                <a href="{{ route('assessments') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="inline-block mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                    {{ __('carbex.assessments.manage') }}
                </a>
                <a href="{{ route('trajectory') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="inline-block mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    {{ __('carbex.targets.trajectory') }}
                </a>
            </div>
        </div>
    </div>
</div>
