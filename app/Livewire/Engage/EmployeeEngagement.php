<?php

namespace App\Livewire\Engage;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * EmployeeEngagement Livewire Component
 *
 * Employee awareness and engagement module with:
 * - Interactive carbon quiz
 * - Individual carbon footprint calculator
 * - Team rankings (opt-in)
 * - Reduction challenges
 *
 * Part of Phase 10: Employee engagement module (T180-T182).
 *
 * @see specs/001-carbex-mvp-platform/tasks.md T180-T182
 */
#[Layout('layouts.app')]
#[Title('Engagement Employés - Carbex')]
class EmployeeEngagement extends Component
{
    // Active tab
    public string $activeTab = 'quiz';

    // Quiz state
    public int $quizStep = 0;
    public array $quizAnswers = [];
    public ?int $quizScore = null;
    public bool $quizCompleted = false;

    // Calculator state
    public array $calculatorInputs = [
        'commute_km' => 0,
        'commute_mode' => 'car_petrol',
        'work_from_home_days' => 0,
        'flights_short' => 0,
        'flights_long' => 0,
        'diet' => 'mixed',
        'heating' => 'gas',
        'electricity_kwh' => 200,
    ];
    public ?array $calculatorResult = null;

    // Challenges state
    public array $activeChallenges = [];
    public array $userChallenges = [];

    // Leaderboard
    public array $leaderboard = [];
    public bool $participateInLeaderboard = false;

    protected array $quizQuestions = [];

    public function mount(): void
    {
        $this->loadQuizQuestions();
        $this->loadChallenges();
        $this->loadLeaderboard();

        $user = Auth::user();
        $this->participateInLeaderboard = $user?->settings['participate_leaderboard'] ?? false;
    }

    protected function loadQuizQuestions(): void
    {
        $this->quizQuestions = [
            [
                'question' => __('carbex.engage.quiz.q1'),
                'options' => [
                    'a' => __('carbex.engage.quiz.q1_a'),
                    'b' => __('carbex.engage.quiz.q1_b'),
                    'c' => __('carbex.engage.quiz.q1_c'),
                    'd' => __('carbex.engage.quiz.q1_d'),
                ],
                'correct' => 'b',
                'explanation' => __('carbex.engage.quiz.q1_explain'),
            ],
            [
                'question' => __('carbex.engage.quiz.q2'),
                'options' => [
                    'a' => __('carbex.engage.quiz.q2_a'),
                    'b' => __('carbex.engage.quiz.q2_b'),
                    'c' => __('carbex.engage.quiz.q2_c'),
                    'd' => __('carbex.engage.quiz.q2_d'),
                ],
                'correct' => 'c',
                'explanation' => __('carbex.engage.quiz.q2_explain'),
            ],
            [
                'question' => __('carbex.engage.quiz.q3'),
                'options' => [
                    'a' => __('carbex.engage.quiz.q3_a'),
                    'b' => __('carbex.engage.quiz.q3_b'),
                    'c' => __('carbex.engage.quiz.q3_c'),
                    'd' => __('carbex.engage.quiz.q3_d'),
                ],
                'correct' => 'a',
                'explanation' => __('carbex.engage.quiz.q3_explain'),
            ],
            [
                'question' => __('carbex.engage.quiz.q4'),
                'options' => [
                    'a' => __('carbex.engage.quiz.q4_a'),
                    'b' => __('carbex.engage.quiz.q4_b'),
                    'c' => __('carbex.engage.quiz.q4_c'),
                    'd' => __('carbex.engage.quiz.q4_d'),
                ],
                'correct' => 'd',
                'explanation' => __('carbex.engage.quiz.q4_explain'),
            ],
            [
                'question' => __('carbex.engage.quiz.q5'),
                'options' => [
                    'a' => __('carbex.engage.quiz.q5_a'),
                    'b' => __('carbex.engage.quiz.q5_b'),
                    'c' => __('carbex.engage.quiz.q5_c'),
                    'd' => __('carbex.engage.quiz.q5_d'),
                ],
                'correct' => 'b',
                'explanation' => __('carbex.engage.quiz.q5_explain'),
            ],
        ];
    }

    public function getQuizQuestions(): array
    {
        return $this->quizQuestions;
    }

    public function getCurrentQuestion(): ?array
    {
        return $this->quizQuestions[$this->quizStep] ?? null;
    }

    public function answerQuiz(string $answer): void
    {
        $this->quizAnswers[$this->quizStep] = $answer;

        if ($this->quizStep < count($this->quizQuestions) - 1) {
            $this->quizStep++;
        } else {
            $this->calculateQuizScore();
        }
    }

    protected function calculateQuizScore(): void
    {
        $correct = 0;

        foreach ($this->quizQuestions as $index => $question) {
            if (isset($this->quizAnswers[$index]) && $this->quizAnswers[$index] === $question['correct']) {
                $correct++;
            }
        }

        $this->quizScore = (int) round(($correct / count($this->quizQuestions)) * 100);
        $this->quizCompleted = true;

        // Save quiz result to user settings
        $user = Auth::user();
        if ($user) {
            $settings = $user->settings ?? [];
            $settings['quiz_score'] = $this->quizScore;
            $settings['quiz_completed_at'] = now()->toDateTimeString();
            $user->update(['settings' => $settings]);
        }
    }

    public function resetQuiz(): void
    {
        $this->quizStep = 0;
        $this->quizAnswers = [];
        $this->quizScore = null;
        $this->quizCompleted = false;
    }

    // Calculator methods

    public function calculateFootprint(): void
    {
        $annualCO2 = 0;

        // Commute emissions (kg CO2 per km per year, 220 working days)
        $commuteFactors = [
            'car_petrol' => 0.21,
            'car_diesel' => 0.17,
            'car_electric' => 0.05,
            'car_hybrid' => 0.12,
            'public_transport' => 0.04,
            'bike' => 0,
            'walk' => 0,
        ];

        $workingDays = 220 - ($this->calculatorInputs['work_from_home_days'] * 52);
        $commuteEmissions = ($this->calculatorInputs['commute_km'] * 2) // round trip
            * $workingDays
            * ($commuteFactors[$this->calculatorInputs['commute_mode']] ?? 0.21);

        $annualCO2 += $commuteEmissions;

        // Flight emissions (kg CO2 per flight)
        $flightEmissions = ($this->calculatorInputs['flights_short'] * 200) // short haul ~200kg
            + ($this->calculatorInputs['flights_long'] * 1000); // long haul ~1000kg

        $annualCO2 += $flightEmissions;

        // Diet emissions (kg CO2 per year)
        $dietFactors = [
            'vegan' => 1500,
            'vegetarian' => 1700,
            'mixed' => 2500,
            'meat_heavy' => 3500,
        ];

        $annualCO2 += $dietFactors[$this->calculatorInputs['diet']] ?? 2500;

        // Home energy (heating)
        $heatingFactors = [
            'gas' => 2000,
            'oil' => 2500,
            'electric' => 1000,
            'heat_pump' => 500,
            'wood' => 300,
        ];

        $annualCO2 += $heatingFactors[$this->calculatorInputs['heating']] ?? 2000;

        // Electricity (kg CO2 per kWh, French mix ~0.05)
        $annualCO2 += $this->calculatorInputs['electricity_kwh'] * 12 * 0.05;

        // Convert to tonnes
        $annualTonnes = $annualCO2 / 1000;

        // Calculate breakdown
        $this->calculatorResult = [
            'total' => round($annualTonnes, 2),
            'breakdown' => [
                'commute' => round($commuteEmissions / 1000, 2),
                'flights' => round($flightEmissions / 1000, 2),
                'diet' => round(($dietFactors[$this->calculatorInputs['diet']] ?? 2500) / 1000, 2),
                'heating' => round(($heatingFactors[$this->calculatorInputs['heating']] ?? 2000) / 1000, 2),
                'electricity' => round(($this->calculatorInputs['electricity_kwh'] * 12 * 0.05) / 1000, 2),
            ],
            'comparison' => $this->getComparison($annualTonnes),
            'tips' => $this->getReductionTips(),
        ];

        // Save to user settings
        $user = Auth::user();
        if ($user) {
            $settings = $user->settings ?? [];
            $settings['carbon_footprint'] = $this->calculatorResult['total'];
            $settings['footprint_calculated_at'] = now()->toDateTimeString();
            $user->update(['settings' => $settings]);
        }
    }

    protected function getComparison(float $tonnes): array
    {
        $frenchAverage = 9.0; // tonnes CO2 per year
        $globalAverage = 4.5; // tonnes CO2 per year
        $parisTarget = 2.0; // tonnes CO2 per year (2050 target)

        return [
            'french_avg' => $frenchAverage,
            'global_avg' => $globalAverage,
            'target' => $parisTarget,
            'vs_french' => round((($tonnes - $frenchAverage) / $frenchAverage) * 100, 1),
            'vs_target' => round((($tonnes - $parisTarget) / $parisTarget) * 100, 1),
        ];
    }

    protected function getReductionTips(): array
    {
        $tips = [];

        if ($this->calculatorInputs['commute_mode'] === 'car_petrol' || $this->calculatorInputs['commute_mode'] === 'car_diesel') {
            $tips[] = __('carbex.engage.calculator.tip_commute');
        }

        if ($this->calculatorInputs['flights_long'] > 2) {
            $tips[] = __('carbex.engage.calculator.tip_flights');
        }

        if ($this->calculatorInputs['diet'] === 'meat_heavy') {
            $tips[] = __('carbex.engage.calculator.tip_diet');
        }

        if ($this->calculatorInputs['heating'] === 'oil' || $this->calculatorInputs['heating'] === 'gas') {
            $tips[] = __('carbex.engage.calculator.tip_heating');
        }

        if ($this->calculatorInputs['work_from_home_days'] < 2) {
            $tips[] = __('carbex.engage.calculator.tip_wfh');
        }

        return array_slice($tips, 0, 3);
    }

    public function resetCalculator(): void
    {
        $this->calculatorInputs = [
            'commute_km' => 0,
            'commute_mode' => 'car_petrol',
            'work_from_home_days' => 0,
            'flights_short' => 0,
            'flights_long' => 0,
            'diet' => 'mixed',
            'heating' => 'gas',
            'electricity_kwh' => 200,
        ];
        $this->calculatorResult = null;
    }

    // Challenges methods

    protected function loadChallenges(): void
    {
        $this->activeChallenges = [
            [
                'id' => 'no_car_week',
                'title' => __('carbex.engage.challenges.no_car_week'),
                'description' => __('carbex.engage.challenges.no_car_week_desc'),
                'points' => 100,
                'duration' => '1 week',
                'icon' => 'car',
                'difficulty' => 'medium',
            ],
            [
                'id' => 'meatless_monday',
                'title' => __('carbex.engage.challenges.meatless_monday'),
                'description' => __('carbex.engage.challenges.meatless_monday_desc'),
                'points' => 50,
                'duration' => '4 weeks',
                'icon' => 'leaf',
                'difficulty' => 'easy',
            ],
            [
                'id' => 'zero_waste_lunch',
                'title' => __('carbex.engage.challenges.zero_waste_lunch'),
                'description' => __('carbex.engage.challenges.zero_waste_lunch_desc'),
                'points' => 75,
                'duration' => '2 weeks',
                'icon' => 'recycle',
                'difficulty' => 'medium',
            ],
            [
                'id' => 'energy_saver',
                'title' => __('carbex.engage.challenges.energy_saver'),
                'description' => __('carbex.engage.challenges.energy_saver_desc'),
                'points' => 150,
                'duration' => '1 month',
                'icon' => 'bolt',
                'difficulty' => 'hard',
            ],
            [
                'id' => 'digital_detox',
                'title' => __('carbex.engage.challenges.digital_detox'),
                'description' => __('carbex.engage.challenges.digital_detox_desc'),
                'points' => 60,
                'duration' => '1 week',
                'icon' => 'device-mobile',
                'difficulty' => 'easy',
            ],
        ];

        // Load user's active challenges
        $user = Auth::user();
        $this->userChallenges = $user?->settings['active_challenges'] ?? [];
    }

    public function joinChallenge(string $challengeId): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $settings = $user->settings ?? [];
        $activeChallenges = $settings['active_challenges'] ?? [];

        if (! isset($activeChallenges[$challengeId])) {
            $activeChallenges[$challengeId] = [
                'joined_at' => now()->toDateTimeString(),
                'status' => 'active',
                'progress' => 0,
            ];

            $settings['active_challenges'] = $activeChallenges;
            $user->update(['settings' => $settings]);

            $this->userChallenges = $activeChallenges;
        }
    }

    public function completeChallenge(string $challengeId): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $settings = $user->settings ?? [];
        $activeChallenges = $settings['active_challenges'] ?? [];

        if (isset($activeChallenges[$challengeId])) {
            $activeChallenges[$challengeId]['status'] = 'completed';
            $activeChallenges[$challengeId]['completed_at'] = now()->toDateTimeString();

            // Add points
            $challenge = collect($this->activeChallenges)->firstWhere('id', $challengeId);
            $settings['engagement_points'] = ($settings['engagement_points'] ?? 0) + ($challenge['points'] ?? 0);

            $settings['active_challenges'] = $activeChallenges;
            $user->update(['settings' => $settings]);

            $this->userChallenges = $activeChallenges;
            $this->loadLeaderboard();
        }
    }

    public function leaveChallenge(string $challengeId): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $settings = $user->settings ?? [];
        $activeChallenges = $settings['active_challenges'] ?? [];

        unset($activeChallenges[$challengeId]);

        $settings['active_challenges'] = $activeChallenges;
        $user->update(['settings' => $settings]);

        $this->userChallenges = $activeChallenges;
    }

    // Leaderboard methods

    protected function loadLeaderboard(): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $users = User::where('organization_id', $organization->id)
            ->whereJsonContains('settings->participate_leaderboard', true)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'initials' => $this->getInitials($user->name),
                    'points' => $user->settings['engagement_points'] ?? 0,
                    'challenges_completed' => collect($user->settings['active_challenges'] ?? [])
                        ->where('status', 'completed')
                        ->count(),
                    'quiz_score' => $user->settings['quiz_score'] ?? null,
                ];
            })
            ->sortByDesc('points')
            ->values()
            ->take(10)
            ->toArray();

        $this->leaderboard = $users;
    }

    protected function getInitials(string $name): string
    {
        $parts = explode(' ', $name);

        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 2));
    }

    public function toggleLeaderboardParticipation(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $settings = $user->settings ?? [];
        $settings['participate_leaderboard'] = ! ($settings['participate_leaderboard'] ?? false);
        $user->update(['settings' => $settings]);

        $this->participateInLeaderboard = $settings['participate_leaderboard'];
        $this->loadLeaderboard();
    }

    public function getUserPoints(): int
    {
        return Auth::user()?->settings['engagement_points'] ?? 0;
    }

    public function getUserRank(): ?int
    {
        $userId = Auth::id();

        foreach ($this->leaderboard as $index => $user) {
            if ($user['id'] === $userId) {
                return $index + 1;
            }
        }

        return null;
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.engage.employee-engagement');
    }
}
