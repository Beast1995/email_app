<?php

namespace App\Services;

use App\Models\EmailCampaign;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Mail\BulkEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BulkEmailService
{
    protected $maxEmailsPerMinute = 10; // Reduced for local testing
    protected $delayBetweenEmails = 2; // Increased delay for better delivery

    public function sendCampaign(EmailCampaign $campaign)
    {
        try {
            $campaign->markAsSending();
            
            $template = $campaign->template;
            $recipients = $campaign->recipients;
            $settings = $campaign->settings ?? [];
            
            $totalRecipients = count($recipients);
            $campaign->update(['total_recipients' => $totalRecipients]);
            
            $sentCount = 0;
            $failedCount = 0;
            
            foreach ($recipients as $index => $recipient) {
                try {
                    // Rate limiting for better delivery
                    if ($index > 0) {
                        sleep($this->delayBetweenEmails); // Wait between emails
                    }
                    
                    $this->sendSingleEmail($campaign, $template, $recipient);
                    $sentCount++;
                    
                    // Log success
                    Log::info("Email sent successfully", [
                        'campaign_id' => $campaign->id,
                        'recipient' => $recipient['email'],
                        'sent_count' => $sentCount
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('Failed to send email to ' . $recipient['email'], [
                        'error' => $e->getMessage(),
                        'campaign_id' => $campaign->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                    $failedCount++;
                }
                
                // Update progress every 5 emails
                if ($index % 5 === 0) {
                    $campaign->update([
                        'sent_count' => $sentCount,
                        'failed_count' => $failedCount
                    ]);
                }
            }
            
            // Final update
            $campaign->update([
                'sent_count' => $sentCount,
                'failed_count' => $failedCount
            ]);
            
            if ($failedCount === 0) {
                $campaign->markAsCompleted();
            } else {
                $campaign->markAsFailed();
            }
            
            return [
                'success' => true,
                'sent' => $sentCount,
                'failed' => $failedCount,
                'total' => $totalRecipients
            ];
            
        } catch (\Exception $e) {
            Log::error('Campaign sending failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $campaign->markAsFailed();
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function sendSingleEmail($campaign, $template, $recipient)
    {
        // Create email log entry
        $emailLog = EmailLog::create([
            'campaign_id' => $campaign->id,
            'to_email' => $recipient['email'],
            'to_name' => $recipient['name'] ?? '',
            'subject' => $template->renderSubject($recipient),
            'content' => $template->renderContent($recipient),
            'status' => 'pending'
        ]);
        
        // Add anti-spam headers
        $emailLog->addAntiSpamHeaders();
        
        // Prepare email data
        $emailData = array_merge($recipient, [
            'campaign_id' => $campaign->id,
            'recipient_id' => $emailLog->id
        ]);
        
        try {
            // Send email with better error handling
            $result = Mail::to($recipient['email'])
                ->send(new BulkEmail($emailData, $template, $recipient));
            
            // Check if email was actually sent
            if ($result) {
                $emailLog->markAsSent();
                Log::info("Email sent to {$recipient['email']}", [
                    'campaign_id' => $campaign->id,
                    'log_id' => $emailLog->id
                ]);
            } else {
                throw new \Exception("Mail service returned false");
            }
            
        } catch (\Exception $e) {
            $emailLog->markAsFailed($e->getMessage());
            Log::error("Failed to send email to {$recipient['email']}", [
                'error' => $e->getMessage(),
                'campaign_id' => $campaign->id,
                'log_id' => $emailLog->id
            ]);
            throw $e;
        }
    }

    public function sendTestEmail($email, $templateId = null)
    {
        try {
            // Get template
            if ($templateId) {
                $template = EmailTemplate::find($templateId);
            } else {
                $template = EmailTemplate::first();
            }
            
            if (!$template) {
                throw new \Exception('No email template found');
            }

            // Sample data
            $sampleData = [
                'name' => 'Test User',
                'company_name' => 'Test Company',
                'support_email' => 'support@example.com',
                'month_year' => date('F Y'),
                'news_content' => 'This is a test newsletter content.',
                'events_content' => 'No upcoming events at this time.',
                'discount_percentage' => '20',
                'promo_code' => 'TEST20',
                'expiry_date' => date('Y-m-d', strtotime('+30 days')),
                'product_name' => 'Test Product',
                'feature_1' => 'Amazing Feature 1',
                'feature_2' => 'Incredible Feature 2',
                'feature_3' => 'Fantastic Feature 3'
            ];

            $recipient = [
                'email' => $email,
                'name' => 'Test User'
            ];

            // Send test email
            $result = Mail::to($email)->send(new BulkEmail($sampleData, $template, $recipient));
            
            if ($result) {
                Log::info("Test email sent successfully to {$email}");
                return [
                    'success' => true,
                    'message' => 'Test email sent successfully!'
                ];
            } else {
                throw new \Exception('Mail service returned false');
            }
            
        } catch (\Exception $e) {
            Log::error("Test email failed to {$email}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function scheduleCampaign(EmailCampaign $campaign, $scheduledAt)
    {
        $campaign->update([
            'status' => 'scheduled',
            'scheduled_at' => Carbon::parse($scheduledAt)
        ]);
    }

    public function getCampaignStats(EmailCampaign $campaign)
    {
        $logs = $campaign->logs;
        
        return [
            'total' => $campaign->total_recipients,
            'sent' => $campaign->sent_count,
            'failed' => $campaign->failed_count,
            'success_rate' => $campaign->success_rate,
            'failure_rate' => $campaign->failure_rate,
            'delivered' => $logs->where('status', 'delivered')->count(),
            'bounced' => $logs->where('status', 'bounced')->count(),
            'spam' => $logs->where('status', 'spam')->count(),
        ];
    }

    public function validateRecipients($recipients)
    {
        $validRecipients = [];
        $invalidEmails = [];
        
        foreach ($recipients as $recipient) {
            if (filter_var($recipient['email'], FILTER_VALIDATE_EMAIL)) {
                $validRecipients[] = $recipient;
            } else {
                $invalidEmails[] = $recipient['email'];
            }
        }
        
        return [
            'valid' => $validRecipients,
            'invalid' => $invalidEmails
        ];
    }
} 