<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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

    public function handleIncomingWebhook(Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = $request->all();
        } else {
            $data = $request->json()->all();
        }

        $id = $data['id'] ?? null;
        $stateId = $data['stateid'] ?? null;
        $status = $data['status'] ?? null;
        $state = $data['state'] ?? null;
        $device = $data['device'] ?? null;
        $target = $data['target'] ?? null;
        $message = $data['message'] ?? null;

        try {
            $findAttributes = [
                'report_id' => $id,
            ];
            
            $customerId = null;
            if (!empty($target)) {
                $customer = Customer::where('phone', $target)->first();
                $customerId = $customer->id ?? null;
            }
            
            $createOrUpdateAttributes = [
                'device' => $device,
                'target' => $target,
                'message' => $message,
                'status' => $status,
                'state' => $state,
                'state_id' => $stateId,
                'customer_id' => $customerId,
            ];
            
            $messageLog = MessageLogs::updateOrCreate(
                $findAttributes,
                $createOrUpdateAttributes
            );
            
            $action = $messageLog->wasRecentlyCreated ? 'created' : 'updated';
            
            return response()->json(['success' => true, 'message' => "Message log {$action} successfully."], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
