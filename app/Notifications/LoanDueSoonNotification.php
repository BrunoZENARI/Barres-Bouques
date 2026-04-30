<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanDueSoonNotification extends Notification
{
    public function __construct(public Loan $loan)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Rappel : retour de livre prévu prochainement')
            ->greeting("Bonjour {$notifiable->prenom} {$notifiable->nom},")
            ->line("Nous vous rappelons que le retour du livre **{$this->loan->book->title}** de *{$this->loan->book->author}* est attendu le **{$this->loan->due_date->format('d/m/Y')}**.")
            ->line('Merci de bien vouloir restituer cet ouvrage dans les délais impartis.')
            ->salutation("Cordialement,\nBibliothèque Barres-Bouques");
    }
}
