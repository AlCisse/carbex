<div class="space-y-6">
    <!-- Header with Points -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('carbex.engage.title') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('carbex.engage.description') }}
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-2 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-2 rounded-lg">
            <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span class="font-semibold text-emerald-700 dark:text-emerald-300">
                {{ __('carbex.engage.your_points') }}: {{ $userPoints }}
            </span>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <nav class="flex space-x-1 overflow-x-auto" aria-label="Tabs">
        @php
            $tabs = [
                'quiz' => __('carbex.engage.tabs.quiz'),
                'calculator' => __('carbex.engage.tabs.calculator'),
                'challenges' => __('carbex.engage.tabs.challenges'),
                'leaderboard' => __('carbex.engage.tabs.leaderboard'),
            ];
        @endphp

        @foreach($tabs as $key => $label)
            <button
                wire:click="setActiveTab('{{ $key }}')"
                class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition-colors
                    {{ $activeTab === $key
                        ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300'
                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </nav>

    <!-- Tab Content -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        @if($activeTab === 'quiz')
            <!-- Quiz Tab -->
            @if(!$quizCompleted)
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('carbex.engage.quiz.question') }}
                        </h2>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $currentQuestion + 1 }} / {{ $this->totalQuestions }}
                        </span>
                    </div>

                    <p class="text-gray-700 dark:text-gray-300 text-lg">
                        {{ $this->currentQuestionData['question'] }}
                    </p>

                    <div class="grid gap-3">
                        @foreach($this->currentQuestionData['options'] as $key => $option)
                            <button
                                wire:click="answerQuiz('{{ $key }}')"
                                class="w-full text-left px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors"
                            >
                                <span class="font-medium text-gray-500 dark:text-gray-400 mr-2">{{ strtoupper($key) }}.</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $option }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Quiz Completed -->
                <div class="text-center space-y-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full {{ $quizScore >= 60 ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-amber-100 dark:bg-amber-900/30' }}">
                        <span class="text-3xl font-bold {{ $quizScore >= 60 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                            {{ $quizScore }}%
                        </span>
                    </div>

                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ __('carbex.engage.quiz.completed') }}
                    </h2>

                    <p class="text-gray-600 dark:text-gray-400">
                        {{ $quizScore >= 80 ? __('carbex.engage.quiz.excellent') : ($quizScore >= 60 ? __('carbex.engage.quiz.good') : __('carbex.engage.quiz.keep_learning')) }}
                    </p>

                    <button
                        wire:click="resetQuiz"
                        class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors"
                    >
                        {{ __('carbex.engage.quiz.retry') }}
                    </button>
                </div>
            @endif

        @elseif($activeTab === 'calculator')
            <!-- Calculator Tab -->
            <div class="space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('carbex.engage.calculator.title') }}
                </h2>

                @if(!$calculatedFootprint)
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Commute -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('carbex.engage.calculator.commute_distance') }}
                            </label>
                            <input
                                type="number"
                                wire:model="calculatorInputs.commute_km"
                                placeholder="km"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('carbex.engage.calculator.commute_mode') }}
                            </label>
                            <select
                                wire:model="calculatorInputs.commute_mode"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="car_petrol">{{ __('carbex.engage.calculator.modes.car_petrol') }}</option>
                                <option value="car_diesel">{{ __('carbex.engage.calculator.modes.car_diesel') }}</option>
                                <option value="car_electric">{{ __('carbex.engage.calculator.modes.car_electric') }}</option>
                                <option value="public_transport">{{ __('carbex.engage.calculator.modes.public_transport') }}</option>
                                <option value="bike">{{ __('carbex.engage.calculator.modes.bike') }}</option>
                                <option value="walk">{{ __('carbex.engage.calculator.modes.walk') }}</option>
                            </select>
                        </div>

                        <!-- Diet -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('carbex.engage.calculator.diet') }}
                            </label>
                            <select
                                wire:model="calculatorInputs.diet"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="vegan">{{ __('carbex.engage.calculator.diets.vegan') }}</option>
                                <option value="vegetarian">{{ __('carbex.engage.calculator.diets.vegetarian') }}</option>
                                <option value="mixed">{{ __('carbex.engage.calculator.diets.mixed') }}</option>
                                <option value="meat_heavy">{{ __('carbex.engage.calculator.diets.meat_heavy') }}</option>
                            </select>
                        </div>

                        <!-- Flights -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('carbex.engage.calculator.flights_short') }}
                            </label>
                            <input
                                type="number"
                                wire:model="calculatorInputs.flights_short"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('carbex.engage.calculator.flights_long') }}
                            </label>
                            <input
                                type="number"
                                wire:model="calculatorInputs.flights_long"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                        </div>

                        <!-- Heating -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('carbex.engage.calculator.heating_type') }}
                            </label>
                            <select
                                wire:model="calculatorInputs.heating_type"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="gas">{{ __('carbex.engage.calculator.heating.gas') }}</option>
                                <option value="oil">{{ __('carbex.engage.calculator.heating.oil') }}</option>
                                <option value="electric">{{ __('carbex.engage.calculator.heating.electric') }}</option>
                                <option value="heat_pump">{{ __('carbex.engage.calculator.heating.heat_pump') }}</option>
                            </select>
                        </div>
                    </div>

                    <button
                        wire:click="calculateFootprint"
                        class="w-full md:w-auto px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors"
                    >
                        {{ __('carbex.engage.calculator.calculate') }}
                    </button>
                @else
                    <!-- Results -->
                    <div class="space-y-6">
                        <div class="text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('carbex.engage.calculator.your_footprint') }}
                            </p>
                            <p class="text-4xl font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $calculatedFootprint }} {{ __('carbex.engage.calculator.tonnes_year') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white mb-3">
                                {{ __('carbex.engage.calculator.breakdown') }}
                            </h3>
                            <div class="space-y-2">
                                @foreach($footprintBreakdown as $category => $value)
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $category) }}</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $value }} t</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button
                            wire:click="$set('calculatedFootprint', null)"
                            class="text-emerald-600 hover:text-emerald-700 font-medium"
                        >
                            {{ __('carbex.engage.calculator.recalculate') }}
                        </button>
                    </div>
                @endif
            </div>

        @elseif($activeTab === 'challenges')
            <!-- Challenges Tab -->
            <div class="space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('carbex.engage.challenges.title') }}
                </h2>

                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($this->availableChallenges as $key => $challenge)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between items-start">
                                <h3 class="font-medium text-gray-900 dark:text-white">
                                    {{ __('carbex.engage.challenges.' . $key) }}
                                </h3>
                                <span class="px-2 py-1 text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded">
                                    {{ $challenge['points'] }} pts
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $challenge['description'] }}
                            </p>
                            <div class="text-xs text-gray-400">
                                {{ __('carbex.engage.challenges.co2_saved') }}: {{ $challenge['co2_saved_kg'] }} kg
                            </div>

                            @if(in_array($key, $activeChallenges))
                                <div class="flex space-x-2">
                                    <button
                                        wire:click="completeChallenge('{{ $key }}')"
                                        class="flex-1 px-3 py-1.5 text-sm bg-emerald-600 hover:bg-emerald-700 text-white rounded font-medium"
                                    >
                                        {{ __('carbex.engage.challenges.mark_complete') }}
                                    </button>
                                    <button
                                        wire:click="leaveChallenge('{{ $key }}')"
                                        class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-700"
                                    >
                                        {{ __('carbex.engage.challenges.leave') }}
                                    </button>
                                </div>
                            @else
                                <button
                                    wire:click="joinChallenge('{{ $key }}')"
                                    class="w-full px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 text-gray-700 dark:text-gray-300 hover:text-emerald-700 dark:hover:text-emerald-300 rounded font-medium transition-colors"
                                >
                                    {{ __('carbex.engage.challenges.join') }}
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        @elseif($activeTab === 'leaderboard')
            <!-- Leaderboard Tab -->
            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('carbex.engage.leaderboard.title') }}
                    </h2>

                    <button
                        wire:click="toggleLeaderboardParticipation"
                        class="text-sm {{ $participateInLeaderboard ? 'text-emerald-600' : 'text-gray-500' }} hover:underline"
                    >
                        {{ __('carbex.engage.leaderboard.participate') }}
                        @if($participateInLeaderboard)
                            <span class="ml-1">&#10003;</span>
                        @endif
                    </button>
                </div>

                @if($participateInLeaderboard)
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('carbex.engage.leaderboard.your_rank') }}
                        </p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                            #{{ $this->userRank }}
                        </p>
                    </div>
                @endif

                <div class="overflow-hidden">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('carbex.engage.leaderboard.rank') }}</th>
                                <th class="py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('carbex.engage.leaderboard.name') }}</th>
                                <th class="py-2 text-right text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('carbex.engage.leaderboard.points') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($this->leaderboard as $entry)
                                <tr class="{{ $entry['rank'] === $this->userRank && $participateInLeaderboard ? 'bg-emerald-50 dark:bg-emerald-900/10' : '' }}">
                                    <td class="py-3 text-sm text-gray-900 dark:text-white font-medium">
                                        @if($entry['rank'] <= 3)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $entry['rank'] === 1 ? 'bg-yellow-100 text-yellow-600' : ($entry['rank'] === 2 ? 'bg-gray-100 text-gray-600' : 'bg-amber-100 text-amber-600') }}">
                                                {{ $entry['rank'] }}
                                            </span>
                                        @else
                                            {{ $entry['rank'] }}
                                        @endif
                                    </td>
                                    <td class="py-3 text-sm text-gray-700 dark:text-gray-300">{{ $entry['name'] }}</td>
                                    <td class="py-3 text-sm text-gray-900 dark:text-white text-right font-medium">{{ $entry['points'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
