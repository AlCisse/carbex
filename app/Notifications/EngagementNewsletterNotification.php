<?php

namespace App\Notifications;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Weekly/monthly engagement newsletter with carbon tips and team updates.
 *
 * Part of Phase 10: Employee engagement module (T182).
 *
 * @see specs/001-carbex-mvp-platform/tasks.md T182
 */
class EngagementNewsletterNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Organization $organization,
        protected array $stats = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->locale ?? config('app.locale', 'fr');
        app()->setLocale($locale);

        $challengesCompleted = $this->stats['challenges_completed'] ?? 0;
        $topPerformer = $this->stats['top_performer'] ?? null;
        $totalPoints = $this->stats['total_points'] ?? 0;
        $tip = $this->getRandomTip($locale);

        $mail = (new MailMessage)
            ->subject(__('carbex.notifications.newsletter.subject', ['org' => $this->organization->name]))
            ->greeting(__('carbex.notifications.newsletter.greeting', ['name' => $notifiable->name]))
            ->line(__('carbex.notifications.newsletter.intro', ['org' => $this->organization->name]));

        // Team stats
        if ($challengesCompleted > 0 || $totalPoints > 0) {
            $mail->line('---')
                ->line(__('carbex.notifications.newsletter.team_stats'))
                ->line(__('carbex.notifications.newsletter.challenges_this_month', ['count' => $challengesCompleted]))
                ->line(__('carbex.notifications.newsletter.total_points', ['count' => $totalPoints]));

            if ($topPerformer) {
                $mail->line(__('carbex.notifications.newsletter.top_performer', ['name' => $topPerformer]));
            }
        }

        // Weekly tip
        $mail->line('---')
            ->line(__('carbex.notifications.newsletter.tip_title'))
            ->line($tip);

        // CTA
        $mail->action(__('carbex.notifications.newsletter.cta'), route('engage.employees'))
            ->line(__('carbex.notifications.newsletter.closing'));

        return $mail->salutation(__('carbex.notifications.newsletter.salutation'));
    }

    protected function getRandomTip(string $locale): string
    {
        $tips = [
            'fr' => [
                'Saviez-vous que le télétravail 2 jours par semaine peut réduire votre empreinte transport de 40% ?',
                'Éteindre votre écran pendant la pause déjeuner peut économiser jusqu\'à 50 kg de CO2 par an.',
                'Un email avec une pièce jointe de 1 Mo émet environ 19g de CO2. Privilégiez les liens partagés !',
                'Réduire la viande rouge d\'un repas par semaine peut diminuer votre empreinte de 200 kg de CO2/an.',
                'Le covoiturage avec un collègue divise par deux les émissions de vos trajets.',
            ],
            'en' => [
                'Did you know that working from home 2 days a week can reduce your transport footprint by 40%?',
                'Turning off your screen during lunch break can save up to 50 kg of CO2 per year.',
                'An email with a 1 MB attachment emits about 19g of CO2. Prefer shared links!',
                'Reducing red meat by one meal per week can decrease your footprint by 200 kg CO2/year.',
                'Carpooling with a colleague cuts your commute emissions in half.',
            ],
            'de' => [
                'Wussten Sie, dass Homeoffice an 2 Tagen pro Woche Ihren Verkehrs-Fußabdruck um 40% reduzieren kann?',
                'Das Ausschalten Ihres Bildschirms in der Mittagspause kann bis zu 50 kg CO2 pro Jahr sparen.',
                'Eine E-Mail mit 1 MB Anhang verursacht etwa 19g CO2. Bevorzugen Sie geteilte Links!',
                'Eine Mahlzeit weniger mit rotem Fleisch pro Woche kann Ihren Fußabdruck um 200 kg CO2/Jahr senken.',
                'Fahrgemeinschaften mit einem Kollegen halbieren Ihre Pendelemissionen.',
            ],
        ];

        $localeTips = $tips[$locale] ?? $tips['en'];

        return $localeTips[array_rand($localeTips)];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'engagement_newsletter',
            'organization_id' => $this->organization->id,
            'stats' => $this->stats,
        ];
    }
}
