<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\BulkEmail;
use App\Models\EmailTemplate;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email} {--template=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'naveenraja907@gmail.com';
        $templateId = $this->option('template');

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');
            return 1;
        }

        // Get template
        $template = EmailTemplate::find($templateId);
        if (!$template) {
            $this->error('Template not found. Available templates:');
            EmailTemplate::all()->each(function ($t) {
                $this->line("ID: {$t->id} - {$t->name}");
            });
            return 1;
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

        try {
            $this->info("Sending test email to: {$email}");
            $this->info("Using template: {$template->name}");
            
            Mail::to($email)->send(new BulkEmail($sampleData, $template, $recipient));
            
            $this->info('✅ Test email sent successfully!');
            $this->info('Check your inbox (and spam folder) for the test email.');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email:');
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }
} 