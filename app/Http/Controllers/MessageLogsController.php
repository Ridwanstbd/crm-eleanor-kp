<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MessageLogs;

class MessageLogsController extends Controller
{
    public function index(Request $request)
    {
        $logs = MessageLogs::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('nomor', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(25);

        return view('pages.Admin.LogMessage.index', compact('logs'));
    }

    public function show(MessageLogs $messageLog)
    {
        return view('pages.Admin.LogMessage.show', compact('messageLog'));
    }

    public function destroy(MessageLogs $messageLog)
    {
        $messageLog->delete();
        return back()->with('success', 'Message log deleted.');
    }
}
