<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Modules\CRM\Lead\Application\Commands\ImportLeadCommand;
use Modules\CRM\Lead\Application\Commands\ImportLeadHandler;

class LeadWebhookController extends Controller
{
    public function receive(Request $request, ImportLeadHandler $handler): JsonResponse
    {
        // Simple token based authentication for webhook
        $token = $request->header('X-Webhook-Token') ?? $request->query('token');
        if (!$token || $token !== config('services.webhook.lead_token', 'default-secure-token-12345')) {
            return response()->json(['error' => 'Unauthorized webhook token'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            
            // Allow codes instead of IDs for easier external integration
            'center_code' => 'required|string',
            'lead_source_code' => 'nullable|string',
            'campaign_code' => 'nullable|string',
            'interest_type_code' => 'nullable|string',
        ]);

        $command = new ImportLeadCommand(
            $validated['name'],
            $validated['phone'],
            $validated['email'] ?? null,
            $validated['center_code'],
            $validated['dob'] ?? null,
            $validated['lead_source_code'] ?? null,
            $validated['campaign_code'] ?? null,
            $validated['interest_type_code'] ?? null
        );

        try {
            $lead = $handler->handle($command);

            return response()->json([
                'success' => true,
                'message' => 'Lead received via webhook successfully',
                'data' => [
                    'id' => $lead->getId()
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 400);
        }
    }
}
