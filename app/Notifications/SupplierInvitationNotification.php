<?php

namespace App\Notifications;

use App\Models\SupplierInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplierInvitationNotification extends Notification implements ShouldQueue
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
        $supplier = $this->invitation->supplier;

        $message = (new MailMessage)
            ->subject("Demande de données carbone - {$organization->name}")
            ->greeting("Bonjour,")
            ->line("{$organization->name} vous invite à partager vos données d'émissions carbone pour l'année {$this->invitation->year}.")
            ->line("En tant que fournisseur, vos données nous aident à calculer notre empreinte carbone Scope 3 et à travailler ensemble vers des objectifs de durabilité.");

        if ($this->invitation->message) {
            $message->line("Message de {$organization->name} :")
                ->line("\"{$this->invitation->message}\"");
        }

        $message->action('Accéder au portail fournisseur', $this->invitation->getPortalUrl())
            ->line("Ce lien est valide jusqu'au {$this->invitation->expires_at->format('d/m/Y')}.")
            ->line("Les données demandées incluent :")
            ->line($this->formatRequestedData())
            ->line("Vos données seront traitées de manière confidentielle conformément à notre politique de confidentialité.")
            ->salutation("Cordialement,\nL'équipe LinsCarbon");

        return $message;
    }

    protected function formatRequestedData(): string
    {
        $labels = [
            'scope1_total' => 'Émissions Scope 1 (combustibles, véhicules)',
            'scope2_location' => 'Émissions Scope 2 location-based',
            'scope2_market' => 'Émissions Scope 2 market-based',
            'scope3_total' => 'Émissions Scope 3 (optionnel)',
            'revenue' => 'Chiffre d\'affaires annuel',
            'employees' => 'Nombre d\'employés',
        ];

        $requested = $this->invitation->requested_data ?? [];

        return collect($requested)
            ->map(fn ($field) => "• " . ($labels[$field] ?? $field))
            ->implode("\n");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'supplier_id' => $this->invitation->supplier_id,
            'organization_id' => $this->invitation->organization_id,
            'year' => $this->invitation->year,
        ];
    }
}
