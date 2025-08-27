<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\Recipient;
use App\Models\EmailGroup;
use App\Services\BulkEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailCampaignController extends Controller
{
    protected $bulkEmailService;

    public function __construct(BulkEmailService $bulkEmailService)
    {
        $this->bulkEmailService = $bulkEmailService;
    }

    public function index()
    {
        $campaigns = EmailCampaign::with('template')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $templates = EmailTemplate::where('is_active', true)->get();
        $groups = EmailGroup::orderBy('name')->get();
        return view('campaigns.create', compact('templates', 'groups'));
    }

    public function store(Request $request)
    {
        // Normalize checkbox to boolean
        $request->merge([
            'only_active' => $request->has('only_active'),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_id' => 'required|exists:email_templates,id',

            // Recipient selection
            'recipient_mode' => 'required|in:manual,groups',

            // Manual recipients (exclude when mode is groups)
            'recipients' => 'exclude_unless:recipient_mode,manual|array|min:1',
            'recipients.*.email' => 'exclude_unless:recipient_mode,manual|required|email',
            'recipients.*.name' => 'exclude_unless:recipient_mode,manual|nullable|string|max:255',

            // Group recipients (required when mode is groups)
            'group_scope' => 'exclude_unless:recipient_mode,groups|required|in:all,specific',
            'group_ids' => 'exclude_unless:recipient_mode,groups|exclude_if:group_scope,all|required|array',
            'group_ids.*' => 'exclude_unless:recipient_mode,groups|exists:email_groups,id',
            'only_active' => 'exclude_unless:recipient_mode,groups|boolean',

            'scheduled_at' => 'nullable|date|after:now',
            'settings' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Build recipients list
        $finalRecipients = [];

        if ($request->recipient_mode === 'groups') {
            $onlyActive = (bool) $request->get('only_active', true);
            $query = Recipient::query();

            if ($request->group_scope === 'specific' && is_array($request->group_ids)) {
                $query->whereIn('email_group_id', $request->group_ids);
            }

            if ($onlyActive) {
                $query->where('is_active', true);
            }

            $finalRecipients = $query->select(['email', 'name'])->get()
                ->map(function ($r) {
                    return ['email' => $r->email, 'name' => $r->name];
                })->toArray();
        } else {
            // Validate manual recipients more strictly
            $validation = $this->bulkEmailService->validateRecipients($request->recipients ?? []);
            if (!empty($validation['invalid'])) {
                return redirect()->back()
                    ->withErrors(['recipients' => 'Invalid emails: ' . implode(', ', $validation['invalid'])])
                    ->withInput();
            }
            $finalRecipients = $validation['valid'];
        }

        if (count($finalRecipients) === 0) {
            return redirect()->back()
                ->withErrors(['recipients' => 'No recipients found based on your selection.'])
                ->withInput();
        }

        $campaign = EmailCampaign::create([
            'name' => $request->name,
            'description' => $request->description,
            'template_id' => $request->template_id,
            'recipients' => array_values($finalRecipients),
            'status' => $request->scheduled_at ? 'scheduled' : 'draft',
            'scheduled_at' => $request->scheduled_at,
            'settings' => $request->settings
        ]);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Email campaign created successfully!');
    }

    public function show(EmailCampaign $campaign)
    {
        $campaign->load('template', 'logs');
        $stats = $this->bulkEmailService->getCampaignStats($campaign);
        
        return view('campaigns.show', compact('campaign', 'stats'));
    }

    public function edit(EmailCampaign $campaign)
    {
        $templates = EmailTemplate::where('is_active', true)->get();
        return view('campaigns.edit', compact('campaign', 'templates'));
    }

    public function update(Request $request, EmailCampaign $campaign)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_id' => 'required|exists:email_templates,id',
            'recipients' => 'required|array|min:1',
            'recipients.*.email' => 'required|email',
            'recipients.*.name' => 'nullable|string|max:255',
            'scheduled_at' => 'nullable|date|after:now',
            'settings' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate recipients
        $validation = $this->bulkEmailService->validateRecipients($request->recipients);
        
        if (!empty($validation['invalid'])) {
            return redirect()->back()
                ->withErrors(['recipients' => 'Invalid emails: ' . implode(', ', $validation['invalid'])])
                ->withInput();
        }

        $campaign->update([
            'name' => $request->name,
            'description' => $request->description,
            'template_id' => $request->template_id,
            'recipients' => $validation['valid'],
            'scheduled_at' => $request->scheduled_at,
            'settings' => $request->settings
        ]);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Email campaign updated successfully!');
    }

    public function destroy(EmailCampaign $campaign)
    {
        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Email campaign deleted successfully!');
    }

    public function send(EmailCampaign $campaign)
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

    public function schedule(EmailCampaign $campaign, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scheduled_at' => 'required|date|after:now'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $this->bulkEmailService->scheduleCampaign($campaign, $request->scheduled_at);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Campaign scheduled successfully!');
    }

    public function duplicate(EmailCampaign $campaign)
    {
        $newCampaign = $campaign->replicate();
        $newCampaign->name = $campaign->name . ' (Copy)';
        $newCampaign->status = 'draft';
        $newCampaign->sent_at = null;
        $newCampaign->sent_count = 0;
        $newCampaign->failed_count = 0;
        $newCampaign->save();

        return redirect()->route('campaigns.show', $newCampaign)
            ->with('success', 'Campaign duplicated successfully!');
    }
} 