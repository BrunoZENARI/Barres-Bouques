<?php

namespace App\Notifications;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanOverdueNotification extends Notification
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
        $daysLate = Carbon::today()->diffInDays($this->loan->due_date);

        return (new MailMessage)
            ->subject('RETARD : retour de livre en attente')
            ->error()
            ->greeting("Bonjour {$notifiable->prenom} {$notifiable->nom},")
            ->line("Le retour du livre **{$this->loan->book->title}** de *{$this->loan->book->author}* était attendu le **{$this->loan->due_date->format('d/m/Y')}**.")
            ->line("Votre emprunt accuse un retard de **{$daysLate} jour(s)**.")
            ->line('Merci de bien vouloir restituer cet ouvrage dès que possible à la bibliothèque Barres-Bouques.')
            ->salutation("Cordialement,\nBibliothèque Barres-Bouques");
    }
}
