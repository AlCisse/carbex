<?php

namespace App\Notifications;

use App\Models\BankConnection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BankConnectionExpired extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public BankConnection $connection
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->locale ?? 'fr';
        $bankName = $this->connection->bank_name;

        if ($locale === 'de') {
            return (new MailMessage)
                ->subject('Bankverbindung erfordert Aufmerksamkeit')
                ->greeting("Hallo {$notifiable->first_name},")
                ->line("Ihre Bankverbindung mit {$bankName} erfordert Ihre Aufmerksamkeit.")
                ->line('Die Verbindung ist möglicherweise abgelaufen oder erfordert eine erneute Authentifizierung.')
                ->action('Verbindung erneuern', url('/banking'))
                ->line('Um weiterhin Ihre Transaktionen automatisch zu importieren, verbinden Sie bitte Ihre Bank erneut.')
                ->salutation('Mit freundlichen Grüßen, LinsCarbon Team');
        }

        return (new MailMessage)
            ->subject('Connexion bancaire nécessite votre attention')
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("Votre connexion bancaire avec {$bankName} nécessite votre attention.")
            ->line('La connexion a peut-être expiré ou nécessite une nouvelle authentification.')
            ->action('Reconnecter ma banque', url('/banking'))
            ->line('Pour continuer à importer automatiquement vos transactions, veuillez reconnecter votre banque.')
            ->salutation('Cordialement, L\'équipe LinsCarbon');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'bank_connection_expired',
            'connection_id' => $this->connection->id,
            'bank_name' => $this->connection->bank_name,
            'status' => $this->connection->status,
            'error_message' => $this->connection->error_message,
        ];
    }
}
