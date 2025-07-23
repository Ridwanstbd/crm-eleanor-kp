<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index()
    {
        return view('pages.Admin.MessageTemplate.index', ['templates' => MessageTemplate::all()]);
    }

    public function create()
    {
        return view('pages.Admin.MessageTemplate.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:message_templates',
            'content' => 'required',
        ]);

        MessageTemplate::create($request->only('name', 'content'));

        return redirect()->route('message-templates.index')->with('success', 'Template created.');
    }

    public function edit(MessageTemplate $messageTemplate)
    {
        return view('pages.Admin.MessageTemplate.edit', compact('messageTemplate'));
    }

    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        $request->validate([
            'name' => 'required|unique:message_templates,name,' . $messageTemplate->id,
            'content' => 'required',
        ]);

        $messageTemplate->update($request->only('name', 'content'));

        return redirect()->route('message-templates.index')->with('success', 'Template updated.');
    }

    public function destroy(MessageTemplate $messageTemplate)
    {
        $messageTemplate->delete();
        return redirect()->route('message-templates.index')->with('success', 'Template deleted.');
    }

}
