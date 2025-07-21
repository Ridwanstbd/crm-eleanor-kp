<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MessageLogs;

class MessageLogsController extends Controller
{
    public function index()
    {
        $logs = MessageLogs::latest()->paginate(25);
        return view('message_logs.index', compact('logs'));
    }

    public function show(MessageLogs $messageLog)
    {
        return view('message_logs.show', compact('messageLog'));
    }

    public function destroy(MessageLogs $messageLog)
    {
        $messageLog->delete();
        return back()->with('success', 'Message log deleted.');
    }
}
