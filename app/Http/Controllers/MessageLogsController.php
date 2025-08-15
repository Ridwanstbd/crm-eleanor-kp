<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MessageLogs;

class MessageLogsController extends Controller
{
    public function index(Request $request)
    {
        $logs = MessageLogs::query()
            ->latest()
            ->paginate(25);

        return view('pages.Admin.LogMessage.index', compact('logs'));
    }

    public function show(MessageLogs $messageLog)
    {
        return view('pages.Admin.LogMessage.show', compact('messageLog'));
    }

    public function handleUpdateStatusWebhook(Request $request)
    {
        if ($request->isMethod('GET')) {
            return response()->json(['success' => true, 'message' => 'Webhook endpoint is active.'], 200);
        }

        try {
            $payload = $request->json()->all();
            
            if (empty($payload)) {
                return response()->json(['success' => false, 'message' => 'Invalid payload.'], 400);
            }
            

            $reportId = $payload['id'] ?? null;
            $stateId = $payload['stateid'] ?? null;
            
            if (!$reportId && !$stateId) {
                return response()->json(['success' => false, 'message' => 'Payload must contain an "id" or "stateid".'], 400);
            }

            $messageLog = null;

            if ($reportId) {
                $messageLog = MessageLogs::where('report_id', $reportId)->first();
                
                if (!$messageLog && $stateId) {
                    $messageLog = MessageLogs::where('state_id', $stateId)->first();
                }
            } else {
                $messageLog = MessageLogs::where('state_id', $stateId)->first();
                
                if ($messageLog) {
                    $reportId = $messageLog->report_id;
                }
            }
            
            if (!$messageLog) {
                return response()->json(['success' => false, 'message' => 'Message log not found.'], 404);
            }

            $attributes = [];
            if (isset($payload['device'])) $attributes['device'] = $payload['device'];
            if (isset($payload['status'])) $attributes['status'] = $payload['status'];
            if (isset($payload['state'])) $attributes['state'] = $payload['state'];
            
            if ($reportId && isset($payload['id'])) {
                $attributes['report_id'] = $reportId;
            }
            
            if ($stateId && isset($payload['stateid'])) {
                $attributes['state_id'] = $stateId;
            }
            
            $messageLog->update($attributes);
            
            return response()->json(['success' => true, 'message' => 'Message log updated successfully.'], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while processing the request.'], 500);
        }
    }
}
