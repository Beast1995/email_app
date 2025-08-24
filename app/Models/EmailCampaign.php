<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'template_id',
        'recipients',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'failed_count',
        'settings'
    ];

    protected $casts = [
        'recipients' => 'array',
        'settings' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    public function logs()
    {
        return $this->hasMany(EmailLog::class, 'campaign_id');
    }

    public function getSuccessRateAttribute()
    {
        if ($this->total_recipients === 0) {
            return 0;
        }
        
        return round(($this->sent_count / $this->total_recipients) * 100, 2);
    }

    public function getFailureRateAttribute()
    {
        if ($this->total_recipients === 0) {
            return 0;
        }
        
        return round(($this->failed_count / $this->total_recipients) * 100, 2);
    }

    public function isReadyToSend()
    {
        return $this->status === 'scheduled' && 
               $this->scheduled_at && 
               $this->scheduled_at->isPast();
    }

    public function markAsSending()
    {
        $this->update(['status' => 'sending']);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'sent_at' => now()
        ]);
    }

    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }
} 