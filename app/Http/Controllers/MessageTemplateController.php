<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortField = $request->input('sort', 'title');
        $sortDirection = $request->input('direction', 'asc');

        $allowedSortFields = ['title'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'title';
        }

        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

        $templates = MessageTemplate::when($search, function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search . '%');
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(10);

        $templates->appends(request()->query());

        return view('pages.Admin.MessageTemplate.index', compact('templates', 'search', 'sortField', 'sortDirection'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:message_templates,title',
            'body' => 'required',
        ]);

        MessageTemplate::create($request->only('title', 'body'));

        return redirect()->route('templates.index')->with('success', 'Template created.');
    }

    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        $request->validate([
            'title' => 'required|unique:message_templates,title,' . $messageTemplate->id,
            'body' => 'required',
        ]);

        $messageTemplate->update($request->only('title', 'body'));

        return redirect()->route('templates.index')->with('success', 'Template updated.');
    }

    public function destroy(MessageTemplate $messageTemplate)
    {
        $messageTemplate->delete();

        return redirect()->route('templates.index')->with('success', 'Template deleted.');
    }
}
