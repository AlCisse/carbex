<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends BaseVerifyEmail implements ShouldQueue
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
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $locale = $notifiable->locale ?? app()->getLocale();

        return (new MailMessage)
            ->subject($this->getSubject($locale))
            ->greeting($this->getGreeting($notifiable, $locale))
            ->line($this->getIntroLine($locale))
            ->action($this->getActionText($locale), $verificationUrl)
            ->line($this->getOutroLine($locale))
            ->salutation($this->getSalutation($locale));
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable): string
    {
        // For API, return the frontend URL that will call the API
        $frontendUrl = config('app.frontend_url', config('app.url'));

        $apiVerificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Extract the path and query from the API URL
        $parsedUrl = parse_url($apiVerificationUrl);
        $path = $parsedUrl['path'] ?? '';
        $query = $parsedUrl['query'] ?? '';

        return "{$frontendUrl}/verify-email?url=" . urlencode("{$path}?{$query}");
    }

    /**
     * Get the subject for the given locale.
     */
    protected function getSubject(string $locale): string
    {
        return match ($locale) {
            'fr' => 'Vérifiez votre adresse email - Carbex',
            'de' => 'Bestätigen Sie Ihre E-Mail-Adresse - Carbex',
            default => 'Verify Your Email Address - Carbex',
        };
    }

    /**
     * Get the greeting for the given notifiable.
     */
    protected function getGreeting($notifiable, string $locale): string
    {
        $name = $notifiable->first_name ?? $notifiable->name ?? '';

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
        return match ($locale) {
            'fr' => 'Merci de vous être inscrit sur Carbex ! Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse email et activer votre compte.',
            'de' => 'Vielen Dank für Ihre Registrierung bei Carbex! Bitte klicken Sie auf die Schaltfläche unten, um Ihre E-Mail-Adresse zu bestätigen und Ihr Konto zu aktivieren.',
            default => 'Thank you for signing up for Carbex! Please click the button below to verify your email address and activate your account.',
        };
    }

    /**
     * Get the action text for the given locale.
     */
    protected function getActionText(string $locale): string
    {
        return match ($locale) {
            'fr' => 'Vérifier mon email',
            'de' => 'E-Mail bestätigen',
            default => 'Verify Email Address',
        };
    }

    /**
     * Get the outro line for the given locale.
     */
    protected function getOutroLine(string $locale): string
    {
        return match ($locale) {
            'fr' => 'Si vous n\'avez pas créé de compte, aucune action n\'est requise.',
            'de' => 'Wenn Sie kein Konto erstellt haben, ist keine weitere Aktion erforderlich.',
            default => 'If you did not create an account, no further action is required.',
        };
    }

    /**
     * Get the salutation for the given locale.
     */
    protected function getSalutation(string $locale): string
    {
        return match ($locale) {
            'fr' => 'L\'équipe Carbex',
            'de' => 'Das Carbex-Team',
            default => 'The Carbex Team',
        };
    }
}
