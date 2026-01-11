@php
    $subscription = auth()->user()?->organization?->subscription;
    $plan = $subscription?->plan ?? 'trial';
    $daysRemaining = $subscription?->trial_ends_at ? now()->diffInDays($subscription->trial_ends_at, false) : 15;
@endphp

<div class="p-4 border-t border-slate-700">
    @if($plan === 'trial' || !$subscription)
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-blue-200 uppercase tracking-wide">Essai Gratuit</span>
                    <p class="text-sm font-bold text-white mt-0.5">{{ max(0, $daysRemaining) }} jours restants</p>
                </div>
                <svg class="h-8 w-8 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <a href="{{ route('billing') }}" class="mt-2 block w-full text-center text-xs font-medium text-white bg-blue-500 hover:bg-blue-400 rounded py-1.5 transition-colors">
                Mettre à niveau
            </a>
        </div>
    @elseif($plan === 'premium')
        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-3">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-300 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <span class="text-sm font-bold text-white">Plan Premium</span>
            </div>
        </div>
    @else
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg p-3">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-purple-300 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <span class="text-sm font-bold text-white">Plan Avancé</span>
            </div>
        </div>
    @endif
</div>
