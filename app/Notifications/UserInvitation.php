<?php

namespace App\Notifications;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The queue connection to use.
     */
    public $connection = 'redis';

    /**
     * The queue to use.
     */
    public $queue = 'notifications';

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Organization $organization,
        public ?User $invitedBy,
        public string $token
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->locale ?? app()->getLocale();
        $invitationUrl = $this->getInvitationUrl();

        return (new MailMessage)
            ->subject($this->getSubject($locale))
            ->greeting($this->getGreeting($notifiable, $locale))
            ->line($this->getIntroLine($locale))
            ->line($this->getOrganizationLine($locale))
            ->action($this->getActionText($locale), $invitationUrl)
            ->line($this->getExpirationLine($locale))
            ->line($this->getOutroLine($locale))
            ->salutation($this->getSalutation($locale));
    }

    /**
     * Get the invitation URL.
     */
    protected function getInvitationUrl(): string
    {
        $frontendUrl = config('app.frontend_url', config('app.url'));

        return "{$frontendUrl}/invitation/{$this->token}";
    }

    /**
     * Get the subject for the given locale.
     */
    protected function getSubject(string $locale): string
    {
        return match ($locale) {
            'fr' => "Invitation a rejoindre {$this->organization->name} sur LinsCarbon",
            'de' => "Einladung zu {$this->organization->name} auf LinsCarbon",
            default => "Invitation to join {$this->organization->name} on LinsCarbon",
        };
    }

    /**
     * Get the greeting for the given notifiable.
     */
    protected function getGreeting(object $notifiable, string $locale): string
    {
        $name = $notifiable->name ?? '';

        return match ($locale) {
            'fr' => "Bonjour {$name},",
            'de' => "Hallo {$name},",
            default => "Hello {$name},",
        };
    }

    /**
     * Get the intro line for the given locale.
     */
    protected function getIntroLine(string $locale): string
    {
        $inviterName = $this->invitedBy?->name ?? __('linscarbon.common.someone');

        return match ($locale) {
            'fr' => "{$inviterName} vous invite a rejoindre l'equipe sur LinsCarbon, la plateforme de suivi d'empreinte carbone.",
            'de' => "{$inviterName} ladt Sie ein, dem Team auf LinsCarbon beizutreten, der Plattform zur CO2-Fussabdruck-Verfolgung.",
            default => "{$inviterName} has invited you to join the team on LinsCarbon, the carbon footprint tracking platform.",
        };
    }

    /**
     * Get the organization line.
     */
    protected function getOrganizationLine(string $locale): string
    {
        return match ($locale) {
            'fr' => "Organisation : **{$this->organization->name}**",
            'de' => "Organisation: **{$this->organization->name}**",
            default => "Organization: **{$this->organization->name}**",
        };
    }

    /**
     * Get the action text for the given locale.
     */
    protected function getActionText(string $locale): string
    {
        return match ($locale) {
            'fr' => 'Accepter l\'invitation',
            'de' => 'Einladung annehmen',
            default => 'Accept Invitation',
        };
    }

    /**
     * Get the expiration line.
     */
    protected function getExpirationLine(string $locale): string
    {
        return match ($locale) {
            'fr' => 'Ce lien d\'invitation expirera dans 7 jours.',
            'de' => 'Dieser Einladungslink lauft in 7 Tagen ab.',
            default => 'This invitation link will expire in 7 days.',
        };
    }

    /**
     * Get the outro line for the given locale.
     */
    protected function getOutroLine(string $locale): string
    {
        return match ($locale) {
            'fr' => 'Si vous n\'attendiez pas cette invitation, vous pouvez ignorer cet email.',
            'de' => 'Wenn Sie diese Einladung nicht erwartet haben, konnen Sie diese E-Mail ignorieren.',
            default => 'If you were not expecting this invitation, you can ignore this email.',
        };
    }

    /**
     * Get the salutation for the given locale.
     */
    protected function getSalutation(string $locale): string
    {
        return match ($locale) {
            'fr' => 'L\'equipe LinsCarbon',
            'de' => 'Das LinsCarbon-Team',
            default => 'The LinsCarbon Team',
        };
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'organization_id' => $this->organization->id,
            'organization_name' => $this->organization->name,
            'invited_by' => $this->invitedBy?->id,
        ];
    }
}
