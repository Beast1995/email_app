<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BulkEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;
    public $template;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct($emailData, $template, $recipient)
    {
        $this->emailData = $emailData;
        $this->template = $template;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->template->renderSubject($this->emailData),
            from: config('mail.from.address'),
            replyTo: config('mail.from.address'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->template->renderContent($this->emailData),
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $this->withHeaders([
            'X-Mailer' => 'Bulk Email App v1.0',
            'X-Priority' => '3',
            'X-MSMail-Priority' => 'Normal',
            'Importance' => 'Normal',
            'X-Unsent' => '1',
            'List-Unsubscribe' => '<mailto:unsubscribe@' . parse_url(config('app.url'), PHP_URL_HOST) . '>',
            'Precedence' => 'bulk',
            'X-Campaign-ID' => $this->emailData['campaign_id'] ?? '',
            'X-Recipient-ID' => $this->emailData['recipient_id'] ?? '',
        ]);

        return $this->view('emails.bulk-email')
                    ->with([
                        'content' => $this->template->renderContent($this->emailData),
                        'recipient' => $this->recipient,
                        'unsubscribeUrl' => $this->generateUnsubscribeUrl()
                    ]);
    }

    private function generateUnsubscribeUrl()
    {
        return url('/unsubscribe/' . base64_encode($this->recipient['email']));
    }
} 