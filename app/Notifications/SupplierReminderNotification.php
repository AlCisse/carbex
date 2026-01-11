<?php

namespace App\Notifications;

use App\Models\SupplierInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplierReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected SupplierInvitation $invitation
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $organization = $this->invitation->organization;
        $daysRemaining = now()->diffInDays($this->invitation->expires_at);

        return (new MailMessage)
            ->subject("Rappel : Données carbone attendues - {$organization->name}")
            ->greeting("Bonjour,")
            ->line("Nous vous rappelons que {$organization->name} attend vos données d'émissions carbone pour l'année {$this->invitation->year}.")
            ->line("Votre lien d'accès expire dans **{$daysRemaining} jours** (le {$this->invitation->expires_at->format('d/m/Y')}).")
            ->action('Compléter maintenant', $this->invitation->getPortalUrl())
            ->line("Votre participation est essentielle pour nous aider à mesurer et réduire notre empreinte carbone collective.")
            ->line("Si vous avez des questions, n'hésitez pas à contacter {$this->invitation->invitedBy->name} à {$this->invitation->invitedBy->email}.")
            ->salutation("Cordialement,\nL'équipe Carbex");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'reminder_count' => $this->invitation->reminder_count + 1,
        ];
    }
}
