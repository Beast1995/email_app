<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Services\BulkEmailService;
use Illuminate\Http\Request;

class CampaignSendController extends Controller
{
    protected $bulkEmailService;

    public function __construct(BulkEmailService $bulkEmailService)
    {
        $this->bulkEmailService = $bulkEmailService;
    }

    public function sendCampaign(EmailCampaign $campaign)
    {
        if ($campaign->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft campaigns can be sent!');
        }

        $result = $this->bulkEmailService->sendCampaign($campaign);

        if ($result['success']) {
            return redirect()->route('campaigns.show', $campaign)
                ->with('success', "Campaign sent successfully! Sent: {$result['sent']}, Failed: {$result['failed']}");
        } else {
            return redirect()->back()
                ->with('error', 'Failed to send campaign: ' . $result['error']);
        }
    }

    public function sendAllDraftCampaigns()
    {
        $campaigns = EmailCampaign::where('status', 'draft')->get();
        
        if ($campaigns->isEmpty()) {
            return redirect()->back()
                ->with('info', 'No draft campaigns found.');
        }

        $successCount = 0;
        $failedCount = 0;
        $totalSent = 0;
        $totalFailed = 0;

        foreach ($campaigns as $campaign) {
            $result = $this->bulkEmailService->sendCampaign($campaign);
            
            if ($result['success']) {
                $successCount++;
                $totalSent += $result['sent'];
                $totalFailed += $result['failed'];
            } else {
                $failedCount++;
            }
        }

        return redirect()->back()
            ->with('success', "Bulk sending completed! Successful campaigns: {$successCount}, Failed: {$failedCount}. Total emails sent: {$totalSent}, Failed: {$totalFailed}");
    }

    public function sendScheduledCampaigns()
    {
        try{
        $campaigns = EmailCampaign::where('statuss', 'scheduled')
           // ->where('scheduled_at', '<=', now())
            ->get();
        
        if ($campaigns->isEmpty()) {
            return redirect()->back()
                ->with('info', 'No scheduled campaigns ready to sendd.');
        }

        $successCount = 0;
        $failedCount = 0;
        $totalSent = 0;
        $totalFailed = 0;

        foreach ($campaigns as $campaign) {
            $result = $this->bulkEmailService->sendCampaign($campaign);
            
            if ($result['success']) {
                $successCount++;
                $totalSent += $result['sent'];
                $totalFailed += $result['failed'];
            } else {
                $failedCount++;
            }
        }

        return redirect()->back()
            ->with('success', "Scheduled campaigns sent! Successful: {$successCount}, Failed: {$failedCount}. Total emails sent: {$totalSent}, Failed: {$totalFailed}");
    }catch(\Exception $e){
        return redirect()->back()
        ->with('error', 'Error sending scheduled campaigns: ' . $e->getMessage());
    }
    }
} 