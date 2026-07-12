<?php

namespace App\Livewire\Engage;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Employee Engagement')]
class EmployeeEngagement extends Component
{
    public string $activeTab = 'quiz';

    // Quiz state
    public int $quizStep = 0;
    public array $quizAnswers = [];
    public bool $quizCompleted = false;
    public ?int $quizScore = null;

    // Calculator state
    public array $calculatorInputs = [
        'commute_km' => 0,
        'commute_mode' => 'car_petrol',
        'diet' => 'mixed',
        'flights_short' => 0,
        'flights_long' => 0,
        'heating' => 'gas',
        'electricity_kwh' => 0,
    ];
    public ?array $calculatorResult = null;

    // Challenges state
    public array $activeChallenges = [];

    // Leaderboard state
    public bool $participateInLeaderboard = false;

    // Points
    public int $userPoints = 0;

    protected array $questions = [
        [
            'question' => 'What is the main greenhouse gas responsible for climate change?',
            'options' => [
                'a' => 'Oxygen (O2)',
                'b' => 'Carbon Dioxide (CO2)',
                'c' => 'Nitrogen (N2)',
                'd' => 'Hydrogen (H2)',
            ],
            'correct' => 'b',
        ],
        [
            'question' => 'Which of these activities produces the most CO2 emissions for an individual?',
            'options' => [
                'a' => 'Taking a short shower',
                'b' => 'Using a laptop for a day',
                'c' => 'Flying long-haul',
                'd' => 'Eating vegetables',
            ],
            'correct' => 'c',
        ],
        [
            'question' => 'What does Scope 1 emissions refer to?',
            'options' => [
                'a' => 'Emissions from electricity use',
                'b' => 'Direct emissions from owned sources',
                'c' => 'Indirect emissions from supply chain',
                'd' => 'Emissions from waste',
            ],
            'correct' => 'a',
        ],
        [
            'question' => 'What percentage of global emissions come from transportation?',
            'options' => [
                'a' => 'About 5%',
                'b' => 'About 16%',
                'c' => 'About 50%',
                'd' => 'About 80%',
            ],
            'correct' => 'd',
        ],
        [
            'question' => 'What is carbon neutrality?',
            'options' => [
                'a' => 'Emitting no carbon at all',
                'b' => 'Balancing emissions with removal/offsets',
                'c' => 'Using only renewable energy',
                'd' => 'Planting trees',
            ],
            'correct' => 'b',
        ],
    ];

    protected array $challenges = [
        'no_car_week' => [
            'title' => 'No Car Week',
            'description' => 'Go a full week without using your car for commuting',
            'points' => 100,
            'duration_days' => 7,
            'co2_saved_kg' => 25,
        ],
        'meatless_monday' => [
            'title' => 'Meatless Monday',
            'description' => 'Avoid meat every Monday for a month',
            'points' => 50,
            'duration_days' => 30,
            'co2_saved_kg' => 12,
        ],
        'energy_saver' => [
            'title' => 'Energy Saver',
            'description' => 'Reduce your electricity consumption by 10% this month',
            'points' => 75,
            'duration_days' => 30,
            'co2_saved_kg' => 20,
        ],
        'bike_to_work' => [
            'title' => 'Bike to Work',
            'description' => 'Cycle to work at least 3 times this week',
            'points' => 60,
            'duration_days' => 7,
            'co2_saved_kg' => 15,
        ],
        'digital_detox' => [
            'title' => 'Digital Detox',
            'description' => 'Reduce screen time and digital device usage',
            'points' => 40,
            'duration_days' => 7,
            'co2_saved_kg' => 5,
        ],
    ];

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            $settings = $user->settings ?? [];
            $this->userPoints = $settings['engagement_points'] ?? 0;
            $this->activeChallenges = $settings['active_challenges'] ?? [];
            $this->participateInLeaderboard = $settings['participate_leaderboard'] ?? false;
            $this->quizScore = $settings['quiz_score'] ?? null;
            $this->quizCompleted = isset($settings['quiz_completed_at']);
        }
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ==================== Quiz Methods ====================

    public function answerQuiz(string $answer): void
    {
        $this->quizAnswers[$this->quizStep] = $answer;

        if ($this->quizStep < count($this->questions) - 1) {
            $this->quizStep++;
        } else {
            $this->completeQuiz();
        }
    }

    protected function completeQuiz(): void
    {
        $this->quizCompleted = true;
        $correct = 0;

        foreach ($this->quizAnswers as $index => $answer) {
            if ($answer === $this->questions[$index]['correct']) {
                $correct++;
            }
        }

        $this->quizScore = (int) round(($correct / count($this->questions)) * 100);

        // Save quiz results to user settings
        $this->updateUserSettings([
            'quiz_score' => $this->quizScore,
            'quiz_completed_at' => now()->toDateTimeString(),
        ]);

        // Award points
        $pointsEarned = $this->quizScore >= 60 ? 20 : 5;
        $this->addPoints($pointsEarned);
    }

    public function resetQuiz(): void
    {
        $this->quizStep = 0;
        $this->quizAnswers = [];
        $this->quizCompleted = false;
        $this->quizScore = null;
    }

    #[Computed]
    public function currentQuestionData(): array
    {
        return $this->questions[$this->quizStep] ?? $this->questions[0];
    }

    #[Computed]
    public function totalQuestions(): int
    {
        return count($this->questions);
    }

    // ==================== Calculator Methods ====================

    public function calculateFootprint(): void
    {
        $breakdown = [];

        // Commute emissions
        $commuteKm = (float) ($this->calculatorInputs['commute_km'] ?? 0);
        $commuteMode = $this->calculatorInputs['commute_mode'] ?? 'car_petrol';
        $emissionFactors = [
            'car_petrol' => 0.21,
            'car_diesel' => 0.17,
            'car_electric' => 0.05,
            'public_transport' => 0.04,
            'bike' => 0,
            'walk' => 0,
        ];
        $commuteEmissions = $commuteKm * 220 * ($emissionFactors[$commuteMode] ?? 0.21);
        $breakdown['commute'] = round($commuteEmissions / 1000, 2);

        // Diet emissions (annual kg CO2)
        $dietFactors = [
            'vegan' => 1500,
            'vegetarian' => 1700,
            'mixed' => 2500,
            'meat_heavy' => 3300,
        ];
        $dietEmissions = $dietFactors[$this->calculatorInputs['diet'] ?? 'mixed'];
        $breakdown['diet'] = round($dietEmissions / 1000, 2);

        // Flight emissions
        $shortFlights = (int) ($this->calculatorInputs['flights_short'] ?? 0);
        $longFlights = (int) ($this->calculatorInputs['flights_long'] ?? 0);
        $flightEmissions = ($shortFlights * 200) + ($longFlights * 1000);
        $breakdown['flights'] = round($flightEmissions / 1000, 2);

        // Home energy - electricity
        $electricityKwh = (float) ($this->calculatorInputs['electricity_kwh'] ?? 2500);
        $electricityEmissions = $electricityKwh * 0.5;
        $breakdown['electricity'] = round($electricityEmissions / 1000, 2);

        // Heating
        $heatingFactors = [
            'gas' => 2000,
            'oil' => 2500,
            'electric' => 1000,
            'heat_pump' => 500,
        ];
        $heatingEmissions = $heatingFactors[$this->calculatorInputs['heating'] ?? 'gas'];
        $breakdown['heating'] = round($heatingEmissions / 1000, 2);

        $total = array_sum($breakdown);

        // Comparison with average
        $averageFootprint = 10.0; // tonnes CO2 per person
        $comparison = [
            'average' => $averageFootprint,
            'difference' => round($total - $averageFootprint, 2),
            'percentage' => round(($total / $averageFootprint) * 100, 0),
        ];

        // Tips based on highest category
        $tips = $this->generateTips($breakdown);

        $this->calculatorResult = [
            'total' => round($total, 2),
            'breakdown' => $breakdown,
            'comparison' => $comparison,
            'tips' => $tips,
        ];

        // Save to user settings
        $this->updateUserSettings([
            'carbon_footprint' => $this->calculatorResult['total'],
            'footprint_calculated_at' => now()->toDateTimeString(),
        ]);

        // Award points for completing calculator
        $this->addPoints(10);
    }

    protected function generateTips(array $breakdown): array
    {
        $tips = [];

        if (($breakdown['commute'] ?? 0) > 2) {
            $tips[] = __('linscarbon.engage.tips.reduce_commute');
        }
        if (($breakdown['flights'] ?? 0) > 1) {
            $tips[] = __('linscarbon.engage.tips.reduce_flights');
        }
        if (($breakdown['diet'] ?? 0) > 2.5) {
            $tips[] = __('linscarbon.engage.tips.reduce_meat');
        }
        if (($breakdown['heating'] ?? 0) > 2) {
            $tips[] = __('linscarbon.engage.tips.improve_insulation');
        }

        if (empty($tips)) {
            $tips[] = __('linscarbon.engage.tips.keep_going');
        }

        return $tips;
    }

    public function resetCalculator(): void
    {
        $this->calculatorInputs = [
            'commute_km' => 0,
            'commute_mode' => 'car_petrol',
            'diet' => 'mixed',
            'flights_short' => 0,
            'flights_long' => 0,
            'heating' => 'gas',
            'electricity_kwh' => 0,
        ];
        $this->calculatorResult = null;
    }

    // ==================== Challenge Methods ====================

    public function joinChallenge(string $challengeKey): void
    {
        if (! isset($this->challenges[$challengeKey])) {
            return;
        }

        if (isset($this->activeChallenges[$challengeKey])) {
            return; // Already joined
        }

        $this->activeChallenges[$challengeKey] = [
            'joined_at' => now()->toDateTimeString(),
            'status' => 'active',
        ];

        $this->updateUserSettings([
            'active_challenges' => $this->activeChallenges,
        ]);
    }

    public function leaveChallenge(string $challengeKey): void
    {
        unset($this->activeChallenges[$challengeKey]);

        $this->updateUserSettings([
            'active_challenges' => $this->activeChallenges,
        ]);
    }

    public function completeChallenge(string $challengeKey): void
    {
        if (! isset($this->activeChallenges[$challengeKey])) {
            return;
        }

        if (($this->activeChallenges[$challengeKey]['status'] ?? '') !== 'active') {
            return;
        }

        $challenge = $this->challenges[$challengeKey] ?? null;
        if ($challenge) {
            $this->addPoints($challenge['points']);
        }

        $this->activeChallenges[$challengeKey]['status'] = 'completed';
        $this->activeChallenges[$challengeKey]['completed_at'] = now()->toDateTimeString();

        $this->updateUserSettings([
            'active_challenges' => $this->activeChallenges,
        ]);
    }

    #[Computed]
    public function availableChallenges(): array
    {
        return $this->challenges;
    }

    // ==================== Leaderboard Methods ====================

    public function toggleLeaderboardParticipation(): void
    {
        $this->participateInLeaderboard = ! $this->participateInLeaderboard;

        $this->updateUserSettings([
            'participate_leaderboard' => $this->participateInLeaderboard,
        ]);
    }

    #[Computed]
    public function leaderboard(): array
    {
        $user = Auth::user();
        if (! $user) {
            return [];
        }

        // Get users from same organization who participate in leaderboard
        $users = User::where('organization_id', $user->organization_id)
            ->get()
            ->filter(function ($u) {
                return ($u->settings['participate_leaderboard'] ?? false) === true;
            })
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'points' => $u->settings['engagement_points'] ?? 0,
                ];
            })
            ->sortByDesc('points')
            ->values()
            ->toArray();

        // Add rank
        foreach ($users as $index => &$userData) {
            $userData['rank'] = $index + 1;
        }

        return $users;
    }

    #[Computed]
    public function userRank(): ?int
    {
        $leaderboard = $this->leaderboard;
        $user = Auth::user();

        foreach ($leaderboard as $index => $userData) {
            if ($userData['id'] === $user?->id) {
                return $index + 1;
            }
        }

        return null;
    }

    // ==================== Helper Methods ====================

    protected function addPoints(int $points): void
    {
        $this->userPoints += $points;

        $this->updateUserSettings([
            'engagement_points' => $this->userPoints,
        ]);
    }

    protected function updateUserSettings(array $newSettings): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $settings = $user->settings ?? [];
        $settings = array_merge($settings, $newSettings);

        $user->update(['settings' => $settings]);
    }

    public function render()
    {
        return view('livewire.engage.employee-engagement');
    }
}
