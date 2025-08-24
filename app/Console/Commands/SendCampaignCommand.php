<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailCampaign;
use App\Services\BulkEmailService;

class SendCampaignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:send {campaign_id?} {--all : Send all draft campaigns} {--scheduled : Send all scheduled campaigns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email campaigns stored in the database';

    protected $bulkEmailService;

    public function __construct(BulkEmailService $bulkEmailService)
    {
        parent::__construct();
        $this->bulkEmailService = $bulkEmailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaignId = $this->argument('campaign_id');
        $sendAll = $this->option('all');
        $sendScheduled = $this->option('scheduled');

        if ($sendAll) {
            $this->sendAllDraftCampaigns();
        } elseif ($sendScheduled) {
            $this->sendAllScheduledCampaigns();
        } elseif ($campaignId) {
            $this->sendSpecificCampaign($campaignId);
        } else {
            $this->showAvailableCampaigns();
        }
    }

    protected function sendSpecificCampaign($campaignId)
    {
        $campaign = EmailCampaign::with('template')->find($campaignId);

        if (!$campaign) {
            $this->error("Campaign with ID {$campaignId} not found.");
            return 1;
        }

        if ($campaign->status !== 'draft') {
            $this->error("Campaign '{$campaign->name}' is not in draft status. Current status: {$campaign->status}");
            return 1;
        }

        $this->info("Sending campaign: {$campaign->name}");
        $this->info("Template: {$campaign->template->name}");
        $this->info("Recipients: " . count($campaign->recipients));

        if (!$this->confirm('Do you want to proceed with sending this campaign?')) {
            $this->info('Campaign sending cancelled.');
            return 0;
        }

        $result = $this->bulkEmailService->sendCampaign($campaign);

        if ($result['success']) {
            $this->info("✅ Campaign sent successfully!");
            $this->info("📧 Sent: {$result['sent']}");
            $this->info("❌ Failed: {$result['failed']}");
            $this->info("📊 Total: {$result['total']}");
        } else {
            $this->error("❌ Failed to send campaign: {$result['error']}");
            return 1;
        }

        return 0;
    }

    protected function sendAllDraftCampaigns()
    {
        $campaigns = EmailCampaign::with('template')
            ->where('status', 'draft')
            ->get();

        if ($campaigns->isEmpty()) {
            $this->info('No draft campaigns found.');
            return 0;
        }

        $this->info("Found {$campaigns->count()} draft campaign(s):");
        $campaigns->each(function ($campaign) {
            $this->line("- {$campaign->name} (ID: {$campaign->id}) - {$campaign->template->name} - " . count($campaign->recipients) . " recipients");
        });

        if (!$this->confirm('Do you want to send all draft campaigns?')) {
            $this->info('Campaign sending cancelled.');
            return 0;
        }

        $successCount = 0;
        $failedCount = 0;

        foreach ($campaigns as $campaign) {
            $this->info("\nSending campaign: {$campaign->name}");
            
            $result = $this->bulkEmailService->sendCampaign($campaign);

            if ($result['success']) {
                $this->info("✅ {$campaign->name} sent successfully!");
                $this->info("   Sent: {$result['sent']}, Failed: {$result['failed']}");
                $successCount++;
            } else {
                $this->error("❌ {$campaign->name} failed: {$result['error']}");
                $failedCount++;
            }
        }

        $this->info("\n📊 Summary:");
        $this->info("✅ Successful: {$successCount}");
        $this->info("❌ Failed: {$failedCount}");

        return 0;
    }

    protected function sendAllScheduledCampaigns()
    {
        $campaigns = EmailCampaign::with('template')
            ->where('status', 'scheduled')
           // ->where('scheduled_at', '<=', now())
            ->get();

        if ($campaigns->isEmpty()) {
            $this->info('No scheduled campaigns ready to send.');
            return 0;
        }

        $this->info("Found {$campaigns->count()} scheduled campaign(s) ready to send:");
        $campaigns->each(function ($campaign) {
            $this->line("- {$campaign->name} (ID: {$campaign->id}) - Scheduled for: {$campaign->scheduled_at}");
        });

        if (!$this->confirm('Do you want to send all scheduled campaigns?')) {
            $this->info('Campaign sending cancelled.');
            return 0;
        }

        $successCount = 0;
        $failedCount = 0;

        foreach ($campaigns as $campaign) {
            $this->info("\nSending scheduled campaign: {$campaign->name}");
            
            $result = $this->bulkEmailService->sendCampaign($campaign);

            if ($result['success']) {
                $this->info("✅ {$campaign->name} sent successfully!");
                $this->info("   Sent: {$result['sent']}, Failed: {$result['failed']}");
                $successCount++;
            } else {
                $this->error("❌ {$campaign->name} failed: {$result['error']}");
                $failedCount++;
            }
        }

        $this->info("\n📊 Summary:");
        $this->info("✅ Successful: {$successCount}");
        $this->info("❌ Failed: {$failedCount}");

        return 0;
    }

    protected function showAvailableCampaigns()
    {
        $this->info('Available campaigns in database:');
        $this->newLine();

        $campaigns = EmailCampaign::with('template')->get();

        if ($campaigns->isEmpty()) {
            $this->info('No campaigns found in database.');
            return 0;
        }

        $headers = ['ID', 'Name', 'Template', 'Status', 'Recipients', 'Created'];
        $rows = [];

        foreach ($campaigns as $campaign) {
            $rows[] = [
                $campaign->id,
                $campaign->name,
                $campaign->template->name,
                $campaign->status,
                count($campaign->recipients),
                $campaign->created_at->format('Y-m-d H:i')
            ];
        }

        $this->table($headers, $rows);

        $this->newLine();
        $this->info('Usage examples:');
        $this->line('  php artisan campaign:send 1                    # Send specific campaign by ID');
        $this->line('  php artisan campaign:send --all                # Send all draft campaigns');
        $this->line('  php artisan campaign:send --scheduled          # Send all scheduled campaigns');
        
        return 0;
    }
} 