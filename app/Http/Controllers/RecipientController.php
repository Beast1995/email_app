<?php

namespace App\Http\Controllers;

use App\Models\Recipient;
use App\Models\EmailGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RecipientController extends Controller
{
    public function index()
    {
        $groups = EmailGroup::orderBy('name')->get();
        $recipients = Recipient::with('group')->orderByDesc('created_at')->paginate(25);
        return view('recipients.index', compact('groups', 'recipients'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required_without:file|nullable|email|unique:recipients,email',
            'name' => 'nullable|string|max:255',
            'email_group_id' => 'nullable|exists:email_groups,id',
            'is_active' => 'nullable|boolean',
            'file' => 'required_without:email|nullable|file|mimes:csv,txt|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Manual entry
        if ($request->filled('email')) {
            Recipient::create([
                'email' => $request->email,
                'name' => $request->name,
                'email_group_id' => $request->email_group_id,
                'is_active' => (bool) $request->get('is_active', true)
            ]);

            return redirect()->route('recipients.index')->with('success', 'Recipient added successfully.');
        }

        // CSV upload
        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            $handle = fopen($path, 'r');
            $row = 0;
            $inserted = 0;
            $skipped = 0;

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $row++;
                if ($row === 1 && str_contains(strtolower(implode(',', $data)), 'email')) {
                    continue; // skip header
                }

                $email = trim($data[0] ?? '');
                $name = trim($data[1] ?? '');

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;
                    continue;
                }

                if (Recipient::where('email', $email)->exists()) {
                    $skipped++;
                    continue;
                }

                Recipient::create([
                    'email' => $email,
                    'name' => $name ?: null,
                    'email_group_id' => $request->email_group_id,
                    'is_active' => (bool) $request->get('is_active', true)
                ]);
                $inserted++;
            }

            fclose($handle);

            return redirect()->route('recipients.index')->with('success', "Upload complete. Inserted: {$inserted}, Skipped: {$skipped}.");
        }

        return redirect()->back()->with('error', 'No data provided.');
    }

    public function toggle(Recipient $recipient)
    {
        $recipient->update(['is_active' => ! $recipient->is_active]);
        return redirect()->back()->with('success', 'Recipient status updated.');
    }

    public function destroy(Recipient $recipient)
    {
        $recipient->delete();
        return redirect()->back()->with('success', 'Recipient deleted.');
    }
} 