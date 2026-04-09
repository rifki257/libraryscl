<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PeminjamanDitolak extends Notification
{
    use Queueable;

    protected $pinjam;
    protected $alasan;

    /**
     * Create a new notification instance.
     */
    public function __construct($pinjam, $alasan)
    {
        $this->pinjam = $pinjam;
        $this->alasan = $alasan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'id_pinjam' => $this->pinjam->id_pinjam,
            'judul_buku' => $this->pinjam->buku->judul,
            'alasan' => $this->alasan,
            'pesan' => 'Peminjaman buku "' . $this->pinjam->buku->judul . '" telah ditolak.',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
