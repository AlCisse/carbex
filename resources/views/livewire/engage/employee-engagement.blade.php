<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('carbex.engage.title') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('carbex.engage.subtitle') }}
            </p>
        </div>

        {{-- User Points Badge --}}
        <div class="flex items-center gap-3 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <div class="text-xs opacity-80">{{ __('carbex.engage.your_points') }}</div>
                <div class="text-xl font-bold">{{ $this->getUserPoints() }}</div>
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
            <button
                wire:click="setActiveTab('quiz')"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'quiz' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                {{ __('carbex.engage.tabs.quiz') }}
            </button>

            <button
                wire:click="setActiveTab('calculator')"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'calculator' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                {{ __('carbex.engage.tabs.calculator') }}
            </button>

            <button
                wire:click="setActiveTab('challenges')"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'challenges' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                {{ __('carbex.engage.tabs.challenges') }}
            </button>

            <button
                wire:click="setActiveTab('leaderboard')"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'leaderboard' ? 'border-green-500 text-green-600 dark:text-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                {{ __('carbex.engage.tabs.leaderboard') }}
            </button>
        </nav>
    </div>

    {{-- Quiz Tab --}}
    @if($activeTab === 'quiz')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            @if($quizCompleted)
                {{-- Quiz Results --}}
                <div class="text-center space-y-6">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full {{ $quizScore >= 80 ? 'bg-green-100 dark:bg-green-900/30' : ($quizScore >= 60 ? 'bg-yellow-100 dark:bg-yellow-900/30' : 'bg-red-100 dark:bg-red-900/30') }}">
                        <span class="text-4xl font-bold {{ $quizScore >= 80 ? 'text-green-600 dark:text-green-400' : ($quizScore >= 60 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                            {{ $quizScore }}%
                        </span>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ __('carbex.engage.quiz.completed') }}
                        </h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            @if($quizScore >= 80)
                                {{ __('carbex.engage.quiz.score_excellent') }}
                            @elseif($quizScore >= 60)
                                {{ __('carbex.engage.quiz.score_good') }}
                            @else
                                {{ __('carbex.engage.quiz.score_improve') }}
                            @endif
                        </p>
                    </div>

                    {{-- Answers Review --}}
                    <div class="mt-6 text-left">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-4">{{ __('carbex.engage.quiz.review') }}</h4>
                        <div class="space-y-4">
                            @foreach($this->getQuizQuestions() as $index => $question)
                                <div class="p-4 rounded-lg {{ ($quizAnswers[$index] ?? '') === $question['correct'] ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' }}">
                                    <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $question['question'] }}</p>
                                    <p class="mt-2 text-sm {{ ($quizAnswers[$index] ?? '') === $question['correct'] ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                                        {{ __('carbex.engage.quiz.your_answer') }}: {{ $question['options'][$quizAnswers[$index] ?? 'a'] ?? '-' }}
                                    </p>
                                    @if(($quizAnswers[$index] ?? '') !== $question['correct'])
                                        <p class="text-sm text-green-700 dark:text-green-300">
                                            {{ __('carbex.engage.quiz.correct_answer') }}: {{ $question['options'][$question['correct']] }}
                                        </p>
                                    @endif
                                    <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">{{ $question['explanation'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button
                        wire:click="resetQuiz"
                        class="mt-6 inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                        {{ __('carbex.engage.quiz.retry') }}
                    </button>
                </div>
            @else
                {{-- Quiz Questions --}}
                @php $currentQuestion = $this->getCurrentQuestion(); @endphp
                @if($currentQuestion)
                    <div class="space-y-6">
                        {{-- Progress --}}
                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ __('carbex.engage.quiz.question') }} {{ $quizStep + 1 }} / {{ count($this->getQuizQuestions()) }}</span>
                            <div class="flex-1 mx-4 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ (($quizStep + 1) / count($this->getQuizQuestions())) * 100 }}%"></div>
                            </div>
                        </div>

                        {{-- Question --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $currentQuestion['question'] }}
                            </h3>
                        </div>

                        {{-- Options --}}
                        <div class="space-y-3">
                            @foreach($currentQuestion['options'] as $key => $option)
                                <button
                                    wire:click="answerQuiz('{{ $key }}')"
                                    class="w-full text-left p-4 rounded-lg border-2 transition-all hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 {{ isset($quizAnswers[$quizStep]) && $quizAnswers[$quizStep] === $key ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }}"
                                >
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 mr-3">
                                        {{ strtoupper($key) }}
                                    </span>
                                    <span class="text-gray-900 dark:text-white">{{ $option }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endif

    {{-- Calculator Tab --}}
    @if($activeTab === 'calculator')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Calculator Form --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('carbex.engage.calculator.title') }}
                </h3>

                <div class="space-y-4">
                    {{-- Commute --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('carbex.engage.calculator.commute_distance') }}
                        </label>
                        <div class="flex items-center gap-2">
                            <input
                                type="number"
                                wire:model="calculatorInputs.commute_km"
                                min="0"
                                max="200"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                            >
                            <span class="text-sm text-gray-500 dark:text-gray-400">km</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('carbex.engage.calculator.commute_mode') }}
                        </label>
                        <select
                            wire:model="calculatorInputs.commute_mode"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                        >
                            <option value="car_petrol">{{ __('carbex.engage.calculator.modes.car_petrol') }}</option>
                            <option value="car_diesel">{{ __('carbex.engage.calculator.modes.car_diesel') }}</option>
                            <option value="car_hybrid">{{ __('carbex.engage.calculator.modes.car_hybrid') }}</option>
                            <option value="car_electric">{{ __('carbex.engage.calculator.modes.car_electric') }}</option>
                            <option value="public_transport">{{ __('carbex.engage.calculator.modes.public_transport') }}</option>
                            <option value="bike">{{ __('carbex.engage.calculator.modes.bike') }}</option>
                            <option value="walk">{{ __('carbex.engage.calculator.modes.walk') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('carbex.engage.calculator.wfh_days') }}
                        </label>
                        <input
                            type="number"
                            wire:model="calculatorInputs.work_from_home_days"
                            min="0"
                            max="5"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                        >
                    </div>

                    {{-- Flights --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('carbex.engage.calculator.flights_short') }}
                            </label>
                            <input
                                type="number"
                                wire:model="calculatorInputs.flights_short"
                                min="0"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
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
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                            >
                        </div>
                    </div>

                    {{-- Diet --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('carbex.engage.calculator.diet') }}
                        </label>
                        <select
                            wire:model="calculatorInputs.diet"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                        >
                            <option value="vegan">{{ __('carbex.engage.calculator.diets.vegan') }}</option>
                            <option value="vegetarian">{{ __('carbex.engage.calculator.diets.vegetarian') }}</option>
                            <option value="mixed">{{ __('carbex.engage.calculator.diets.mixed') }}</option>
                            <option value="meat_heavy">{{ __('carbex.engage.calculator.diets.meat_heavy') }}</option>
                        </select>
                    </div>

                    {{-- Heating --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('carbex.engage.calculator.heating') }}
                        </label>
                        <select
                            wire:model="calculatorInputs.heating"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                        >
                            <option value="gas">{{ __('carbex.engage.calculator.heating_types.gas') }}</option>
                            <option value="oil">{{ __('carbex.engage.calculator.heating_types.oil') }}</option>
                            <option value="electric">{{ __('carbex.engage.calculator.heating_types.electric') }}</option>
                            <option value="heat_pump">{{ __('carbex.engage.calculator.heating_types.heat_pump') }}</option>
                            <option value="wood">{{ __('carbex.engage.calculator.heating_types.wood') }}</option>
                        </select>
                    </div>

                    {{-- Electricity --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('carbex.engage.calculator.electricity') }}
                        </label>
                        <div class="flex items-center gap-2">
                            <input
                                type="number"
                                wire:model="calculatorInputs.electricity_kwh"
                                min="0"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                            >
                            <span class="text-sm text-gray-500 dark:text-gray-400">kWh/{{ __('carbex.engage.calculator.month') }}</span>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button
                            wire:click="calculateFootprint"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            {{ __('carbex.engage.calculator.calculate') }}
                        </button>
                        <button
                            wire:click="resetCalculator"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            {{ __('carbex.engage.calculator.reset') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Results --}}
            <div class="space-y-6">
                @if($calculatorResult)
                    {{-- Total --}}
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-sm p-6 text-white">
                        <div class="text-center">
                            <p class="text-sm opacity-80">{{ __('carbex.engage.calculator.your_footprint') }}</p>
                            <p class="text-5xl font-bold mt-2">{{ $calculatorResult['total'] }}</p>
                            <p class="text-lg">{{ __('carbex.engage.calculator.tonnes_year') }}</p>
                        </div>

                        {{-- Comparison --}}
                        <div class="mt-6 grid grid-cols-3 gap-4 text-center text-sm">
                            <div>
                                <p class="opacity-80">{{ __('carbex.engage.calculator.french_avg') }}</p>
                                <p class="font-semibold">{{ $calculatorResult['comparison']['french_avg'] }} t</p>
                            </div>
                            <div>
                                <p class="opacity-80">{{ __('carbex.engage.calculator.global_avg') }}</p>
                                <p class="font-semibold">{{ $calculatorResult['comparison']['global_avg'] }} t</p>
                            </div>
                            <div>
                                <p class="opacity-80">{{ __('carbex.engage.calculator.paris_target') }}</p>
                                <p class="font-semibold">{{ $calculatorResult['comparison']['target'] }} t</p>
                            </div>
                        </div>
                    </div>

                    {{-- Breakdown --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-4">{{ __('carbex.engage.calculator.breakdown') }}</h4>
                        <div class="space-y-3">
                            @foreach($calculatorResult['breakdown'] as $category => $value)
                                @php
                                    $percentage = $calculatorResult['total'] > 0 ? ($value / $calculatorResult['total']) * 100 : 0;
                                    $colors = [
                                        'commute' => 'bg-blue-500',
                                        'flights' => 'bg-purple-500',
                                        'diet' => 'bg-orange-500',
                                        'heating' => 'bg-red-500',
                                        'electricity' => 'bg-yellow-500',
                                    ];
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600 dark:text-gray-400">{{ __('carbex.engage.calculator.categories.' . $category) }}</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $value }} t</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="{{ $colors[$category] ?? 'bg-green-500' }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tips --}}
                    @if(count($calculatorResult['tips']) > 0)
                        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800 p-6">
                            <h4 class="font-medium text-amber-800 dark:text-amber-200 mb-3">{{ __('carbex.engage.calculator.tips_title') }}</h4>
                            <ul class="space-y-2">
                                @foreach($calculatorResult['tips'] as $tip)
                                    <li class="flex items-start gap-2 text-sm text-amber-700 dark:text-amber-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $tip }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @else
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 p-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">{{ __('carbex.engage.calculator.fill_form') }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Challenges Tab --}}
    @if($activeTab === 'challenges')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activeChallenges as $challenge)
                @php
                    $userChallenge = $userChallenges[$challenge['id']] ?? null;
                    $isJoined = $userChallenge !== null;
                    $isCompleted = ($userChallenge['status'] ?? '') === 'completed';
                    $difficultyColors = [
                        'easy' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                        'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                        'hard' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                    ];
                    $icons = [
                        'car' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />',
                        'leaf' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />',
                        'recycle' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />',
                        'bolt' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
                        'device-mobile' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />',
                    ];
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 {{ $isCompleted ? 'ring-2 ring-green-500' : '' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg {{ $isCompleted ? 'bg-green-100 dark:bg-green-900/30' : 'bg-gray-100 dark:bg-gray-700' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ $isCompleted ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    {!! $icons[$challenge['icon']] ?? $icons['leaf'] !!}
                                </svg>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $difficultyColors[$challenge['difficulty']] ?? $difficultyColors['medium'] }}">
                                {{ __('carbex.engage.challenges.difficulty.' . $challenge['difficulty']) }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $challenge['points'] }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 block">pts</span>
                        </div>
                    </div>

                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $challenge['title'] }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ $challenge['description'] }}
                    </p>

                    <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span>{{ __('carbex.engage.challenges.duration') }}: {{ $challenge['duration'] }}</span>
                    </div>

                    <div class="mt-4">
                        @if($isCompleted)
                            <div class="flex items-center gap-2 text-green-600 dark:text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium">{{ __('carbex.engage.challenges.completed') }}</span>
                            </div>
                        @elseif($isJoined)
                            <div class="flex gap-2">
                                <button
                                    wire:click="completeChallenge('{{ $challenge['id'] }}')"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                >
                                    {{ __('carbex.engage.challenges.mark_complete') }}
                                </button>
                                <button
                                    wire:click="leaveChallenge('{{ $challenge['id'] }}')"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    {{ __('carbex.engage.challenges.leave') }}
                                </button>
                            </div>
                        @else
                            <button
                                wire:click="joinChallenge('{{ $challenge['id'] }}')"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-600 rounded-lg text-sm font-medium text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                {{ __('carbex.engage.challenges.join') }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Leaderboard Tab --}}
    @if($activeTab === 'leaderboard')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Leaderboard Table --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('carbex.engage.leaderboard.title') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('carbex.engage.leaderboard.subtitle') }}
                    </p>
                </div>

                @if(count($leaderboard) > 0)
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($leaderboard as $index => $user)
                            <li class="p-4 flex items-center gap-4 {{ $user['id'] === auth()->id() ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                {{-- Rank --}}
                                <div class="flex-shrink-0 w-8 text-center">
                                    @if($index === 0)
                                        <span class="text-2xl">🥇</span>
                                    @elseif($index === 1)
                                        <span class="text-2xl">🥈</span>
                                    @elseif($index === 2)
                                        <span class="text-2xl">🥉</span>
                                    @else
                                        <span class="text-lg font-semibold text-gray-500 dark:text-gray-400">{{ $index + 1 }}</span>
                                    @endif
                                </div>

                                {{-- Avatar --}}
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-medium">
                                    {{ $user['initials'] }}
                                </div>

                                {{-- Name --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $user['name'] }}
                                        @if($user['id'] === auth()->id())
                                            <span class="text-xs text-green-600 dark:text-green-400">({{ __('carbex.engage.leaderboard.you') }})</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $user['challenges_completed'] }} {{ __('carbex.engage.leaderboard.challenges_completed') }}
                                    </p>
                                </div>

                                {{-- Points --}}
                                <div class="text-right">
                                    <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ $user['points'] }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">pts</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">{{ __('carbex.engage.leaderboard.no_participants') }}</p>
                    </div>
                @endif
            </div>

            {{-- Participation Settings --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('carbex.engage.leaderboard.settings') }}
                </h4>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ __('carbex.engage.leaderboard.participate') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ __('carbex.engage.leaderboard.participate_desc') }}
                        </p>
                    </div>
                    <button
                        wire:click="toggleLeaderboardParticipation"
                        type="button"
                        class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 {{ $participateInLeaderboard ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-700' }}"
                    >
                        <span class="sr-only">Toggle participation</span>
                        <span class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $participateInLeaderboard ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                @if($participateInLeaderboard)
                    <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                    {{ __('carbex.engage.leaderboard.your_rank') }}: #{{ $this->getUserRank() ?? '-' }}
                                </p>
                                <p class="text-xs text-green-600 dark:text-green-400">
                                    {{ $this->getUserPoints() }} {{ __('carbex.engage.leaderboard.points') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- How to earn points --}}
                <div class="mt-6">
                    <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                        {{ __('carbex.engage.leaderboard.how_to_earn') }}
                    </h5>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <span class="text-green-600 dark:text-green-400">+50-150</span>
                            {{ __('carbex.engage.leaderboard.earn_challenges') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-green-600 dark:text-green-400">+25</span>
                            {{ __('carbex.engage.leaderboard.earn_quiz') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-green-600 dark:text-green-400">+10</span>
                            {{ __('carbex.engage.leaderboard.earn_calculator') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
