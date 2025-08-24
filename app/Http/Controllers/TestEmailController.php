<?php

namespace App\Http\Controllers;

use App\Services\BulkEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailController extends Controller
{
    protected $bulkEmailService;

    public function __construct(BulkEmailService $bulkEmailService)
    {
        $this->bulkEmailService = $bulkEmailService;
    }

    public function showTestForm()
    {
        return view('test-email');
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'template_id' => 'nullable|exists:email_templates,id'
        ]);

        $email = $request->email;
        $templateId = $request->template_id;

        try {
            // Test basic mail configuration
            $this->testMailConfiguration();
            
            // Send test email
            $result = $this->bulkEmailService->sendTestEmail($email, $templateId);
            
            if ($result['success']) {
                return redirect()->back()
                    ->with('success', 'Test email sent successfully! Check your inbox and spam folder.');
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to send test email: ' . $result['error']);
            }
            
        } catch (\Exception $e) {
            Log::error('Test email failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Test email failed: ' . $e->getMessage());
        }
    }

    protected function testMailConfiguration()
    {
        // Test mail configuration
        $config = config('mail');
        
        Log::info('Mail configuration test', [
            'default' => $config['default'],
            'from_address' => $config['from']['address'],
            'from_name' => $config['from']['name'],
            'mailers' => array_keys($config['mailers'])
        ]);

        // Test SMTP connection if using SMTP
        if ($config['default'] === 'smtp') {
            $smtpConfig = $config['mailers']['smtp'];
            Log::info('SMTP configuration', [
                'host' => $smtpConfig['host'],
                'port' => $smtpConfig['port'],
                'encryption' => $smtpConfig['encryption'],
                'username' => $smtpConfig['username'] ? 'Set' : 'Not set'
            ]);
        }
    }

    public function checkMailLogs()
    {
        $logs = \App\Models\EmailLog::with('campaign')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('mail-logs', compact('logs'));
    }

    public function resendFailedEmails()
    {
        $failedLogs = \App\Models\EmailLog::where('status', 'failed')
            ->with('campaign.template')
            ->get();

        $resentCount = 0;
        $errors = [];

        foreach ($failedLogs as $log) {
            try {
                $recipient = [
                    'email' => $log->to_email,
                    'name' => $log->to_name
                ];

                $result = Mail::to($log->to_email)
                    ->send(new \App\Mail\BulkEmail([], $log->campaign->template, $recipient));

                if ($result) {
                    $log->update(['status' => 'sent', 'sent_at' => now()]);
                    $resentCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to resend to {$log->to_email}: " . $e->getMessage();
            }
        }

        return redirect()->back()
            ->with('success', "Resent {$resentCount} emails successfully.")
            ->with('errors', $errors);
    }
} 