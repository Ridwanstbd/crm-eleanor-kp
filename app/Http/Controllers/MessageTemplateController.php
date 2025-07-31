<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');

        $allowedSortFields = ['name'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'name';
        }

        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

        $templates = MessageTemplate::when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(10);

        $templates->appends(request()->query());

        return view('pages.Admin.MessageTemplate.index', compact('templates', 'search', 'sortField', 'sortDirection'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:message_templates,name',
            'content' => 'required',
        ]);

        MessageTemplate::create($request->only('name', 'content'));

        return redirect()->route('templates.index')->with('success', 'Template created.');
    }

    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        $request->validate([
            'name' => 'required|unique:message_templates,name,' . $messageTemplate->id,
            'content' => 'required',
        ]);

        $messageTemplate->update($request->only('name', 'content'));

        return redirect()->route('templates.index')->with('success', 'Template updated.');
    }

    public function destroy(MessageTemplate $messageTemplate)
    {
        $messageTemplate->delete();

        return redirect()->route('templates.index')->with('success', 'Template deleted.');
    }
}
