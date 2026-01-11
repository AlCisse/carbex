<?php

namespace App\Livewire\Engage;

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
    public int $currentQuestion = 0;
    public array $answers = [];
    public bool $quizCompleted = false;
    public ?int $quizScore = null;

    // Calculator state
    public array $calculatorInputs = [
        'commute_km' => '',
        'commute_mode' => 'car_petrol',
        'diet' => 'mixed',
        'flights_short' => 0,
        'flights_long' => 0,
        'heating_type' => 'gas',
        'electricity_kwh' => '',
    ];
    public ?float $calculatedFootprint = null;
    public ?array $footprintBreakdown = null;

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
            'correct' => 'b',
        ],
        [
            'question' => 'What percentage of global emissions come from transportation?',
            'options' => [
                'a' => 'About 5%',
                'b' => 'About 16%',
                'c' => 'About 50%',
                'd' => 'About 80%',
            ],
            'correct' => 'b',
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
    ];

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            // Use session storage for engagement data
            $key = 'engage_' . $user->id;
            $this->userPoints = session($key . '_points', 0);
            $this->activeChallenges = session($key . '_challenges', []);
            $this->participateInLeaderboard = session($key . '_leaderboard', false);
        }
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ==================== Quiz Methods ====================

    public function answerQuiz(string $answer): void
    {
        $this->answers[$this->currentQuestion] = $answer;

        if ($this->currentQuestion < count($this->questions) - 1) {
            $this->currentQuestion++;
        } else {
            $this->completeQuiz();
        }
    }

    protected function completeQuiz(): void
    {
        $this->quizCompleted = true;
        $correct = 0;

        foreach ($this->answers as $index => $answer) {
            if ($answer === $this->questions[$index]['correct']) {
                $correct++;
            }
        }

        $this->quizScore = (int) round(($correct / count($this->questions)) * 100);

        // Award points
        $pointsEarned = $this->quizScore >= 60 ? 20 : 5;
        $this->addPoints($pointsEarned);
    }

    public function resetQuiz(): void
    {
        $this->currentQuestion = 0;
        $this->answers = [];
        $this->quizCompleted = false;
        $this->quizScore = null;
    }

    #[Computed]
    public function currentQuestionData(): array
    {
        return $this->questions[$this->currentQuestion] ?? $this->questions[0];
    }

    #[Computed]
    public function totalQuestions(): int
    {
        return count($this->questions);
    }

    // ==================== Calculator Methods ====================

    public function calculateFootprint(): void
    {
        $footprint = 0;
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
        $commuteEmissions = $commuteKm * 220 * ($emissionFactors[$commuteMode] ?? 0.21); // 220 working days
        $breakdown['commute'] = round($commuteEmissions / 1000, 2);
        $footprint += $commuteEmissions;

        // Diet emissions (annual kg CO2)
        $dietFactors = [
            'vegan' => 1500,
            'vegetarian' => 1700,
            'mixed' => 2500,
            'meat_heavy' => 3300,
        ];
        $dietEmissions = $dietFactors[$this->calculatorInputs['diet'] ?? 'mixed'];
        $breakdown['diet'] = round($dietEmissions / 1000, 2);
        $footprint += $dietEmissions;

        // Flight emissions
        $shortFlights = (int) ($this->calculatorInputs['flights_short'] ?? 0);
        $longFlights = (int) ($this->calculatorInputs['flights_long'] ?? 0);
        $flightEmissions = ($shortFlights * 200) + ($longFlights * 1000);
        $breakdown['flights'] = round($flightEmissions / 1000, 2);
        $footprint += $flightEmissions;

        // Home energy
        $electricityKwh = (float) ($this->calculatorInputs['electricity_kwh'] ?? 2500);
        $electricityEmissions = $electricityKwh * 0.5; // Average grid factor
        $breakdown['electricity'] = round($electricityEmissions / 1000, 2);
        $footprint += $electricityEmissions;

        $heatingFactors = [
            'gas' => 2000,
            'oil' => 2500,
            'electric' => 1000,
            'heat_pump' => 500,
        ];
        $heatingEmissions = $heatingFactors[$this->calculatorInputs['heating_type'] ?? 'gas'];
        $breakdown['heating'] = round($heatingEmissions / 1000, 2);
        $footprint += $heatingEmissions;

        $this->calculatedFootprint = round($footprint / 1000, 2); // Convert to tonnes
        $this->footprintBreakdown = $breakdown;

        // Award points for completing calculator
        $this->addPoints(10);
    }

    // ==================== Challenge Methods ====================

    public function joinChallenge(string $challengeKey): void
    {
        if (! isset($this->challenges[$challengeKey])) {
            return;
        }

        if (in_array($challengeKey, $this->activeChallenges)) {
            return; // Already joined
        }

        $this->activeChallenges[] = $challengeKey;
        $this->saveUserSettings();
    }

    public function leaveChallenge(string $challengeKey): void
    {
        $this->activeChallenges = array_filter(
            $this->activeChallenges,
            fn ($c) => $c !== $challengeKey
        );
        $this->saveUserSettings();
    }

    public function completeChallenge(string $challengeKey): void
    {
        if (! in_array($challengeKey, $this->activeChallenges)) {
            return;
        }

        $challenge = $this->challenges[$challengeKey] ?? null;
        if ($challenge) {
            $this->addPoints($challenge['points']);
        }

        $this->leaveChallenge($challengeKey);
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
        $this->saveUserSettings();
    }

    #[Computed]
    public function leaderboard(): array
    {
        // Mock leaderboard data
        return [
            ['name' => 'Marie D.', 'points' => 450, 'rank' => 1],
            ['name' => 'Jean P.', 'points' => 380, 'rank' => 2],
            ['name' => 'Sophie L.', 'points' => 320, 'rank' => 3],
            ['name' => Auth::user()?->name ?? 'You', 'points' => $this->userPoints, 'rank' => 4],
            ['name' => 'Pierre M.', 'points' => 180, 'rank' => 5],
        ];
    }

    #[Computed]
    public function userRank(): int
    {
        return 4; // Mock rank
    }

    // ==================== Helper Methods ====================

    protected function addPoints(int $points): void
    {
        $this->userPoints += $points;
        $this->saveUserSettings();
    }

    protected function saveUserSettings(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        // Use session storage for engagement data
        $key = 'engage_' . $user->id;
        session([$key . '_points' => $this->userPoints]);
        session([$key . '_challenges' => $this->activeChallenges]);
        session([$key . '_leaderboard' => $this->participateInLeaderboard]);
    }

    public function render()
    {
        return view('livewire.engage.employee-engagement');
    }
}
