<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $url;
    public $content;
    public $spkNumber;
    public $debitur;
    public $bukti;
    public $kol;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $url, $content, $spkNumber, $debitur, $bukti, $kol)
    {
        //
        $this->user = $user;
        $this->url = $url;
        $this->content = $content;
        $this->spkNumber = $spkNumber;
        $this->debitur = $debitur;
        $this->bukti = $bukti;
        $this->kol = $kol;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Peringatan Tagihan Piutang Pembiayaan',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification_mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        $spkMap = [
            1 => ['view' => 'emails.surat_peringatan1', 'file' => 'Surat Peringatan 1.pdf'],
            2 => ['view' => 'emails.surat_peringatan2', 'file' => 'Surat Peringatan 2.pdf'],
            3 => ['view' => 'emails.surat_peringatan3', 'file' => 'Surat Peringatan 3.pdf'],
        ];

        // Default to SP1 if spkNumber is invalid
        $spk = $spkMap[$this->spkNumber] ?? $spkMap[1];
        $invoice = $this->bukti->no_invoice ?? 'N/A';
        $kol = $this->kol;
        $debitur = $this->debitur;

        $pdf = Pdf::loadView($spk['view'], [
            'invoice' => $invoice,
            'kol' => $kol,
            'debitur' => $debitur,
        ])->setPaper('a4', 'portrait');

        $attachments[] = Attachment::fromData(
            fn () => $pdf->output(),
            $spk['file']
        )->withMime('application/pdf');

        return $attachments;
    }
}
