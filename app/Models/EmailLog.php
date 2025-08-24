<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'to_email',
        'to_name',
        'subject',
        'content',
        'status',
        'error_message',
        'message_id',
        'sent_at',
        'delivered_at',
        'headers'
    ];

    protected $casts = [
        'headers' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    public function campaign()
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    public function markAsSent($messageId = null)
    {
        $this->update([
            'status' => 'sent',
            'message_id' => $messageId,
            'sent_at' => now()
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }

    public function markAsBounced()
    {
        $this->update(['status' => 'bounced']);
    }

    public function markAsSpam()
    {
        $this->update(['status' => 'spam']);
    }

    public function addAntiSpamHeaders()
    {
        $headers = $this->headers ?? [];
        
        // Add anti-spam headers
        $headers['X-Mailer'] = 'Bulk Email App v1.0';
        $headers['X-Priority'] = '3';
        $headers['X-MSMail-Priority'] = 'Normal';
        $headers['Importance'] = 'Normal';
        $headers['X-Unsent'] = '1';
        $headers['List-Unsubscribe'] = '<mailto:unsubscribe@' . parse_url(config('app.url'), PHP_URL_HOST) . '>';
        $headers['Precedence'] = 'bulk';
        
        $this->update(['headers' => $headers]);
    }
} 