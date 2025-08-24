<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('created_at', 'desc')->get();
        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        EmailTemplate::create($request->all());

        return redirect()->route('templates.index')
            ->with('success', 'Email template created successfully!');
    }

    public function show(EmailTemplate $template)
    {
        return view('templates.show', compact('template'));
    }

    public function edit(EmailTemplate $template)
    {
        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, EmailTemplate $template)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $template->update($request->all());

        return redirect()->route('templates.index')
            ->with('success', 'Email template updated successfully!');
    }

    public function destroy(EmailTemplate $template)
    {
        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Email template deleted successfully!');
    }

    public function preview(Request $request, EmailTemplate $template)
    {
        $sampleData = $request->get('sample_data', []);
        
        $renderedSubject = $template->renderSubject($sampleData);
        $renderedContent = $template->renderContent($sampleData);
        
        return response()->json([
            'subject' => $renderedSubject,
            'content' => $renderedContent
        ]);
    }
} 