<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Modules\CRM\Lead\Application\Commands\CreateLeadCommand;
use Modules\CRM\Lead\Application\Commands\CreateLeadHandler;
use Modules\CRM\Source\Infrastructure\ReadModels\SourceReadModel;
use Modules\CRM\Campaign\Infrastructure\ReadModels\CampaignReadModel;
use Modules\CRM\InterestType\Infrastructure\ReadModels\InterestTypeReadModel;
use Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel;

class LeadWebhookController extends Controller
{
    public function receive(Request $request, CreateLeadHandler $handler): JsonResponse
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
            'source_code' => 'nullable|string',
            'campaign_code' => 'nullable|string',
            'interest_type_code' => 'nullable|string',
        ]);

        // Auto Map Codes to UUIDs
        $centerId = null;
        if (!empty($validated['center_code'])) {
            $center = CenterReadModel::where('code', $validated['center_code'])->first();
            if (!$center) {
                return response()->json(['error' => 'Center code not found'], 400);
            }
            $centerId = $center->id;
        }

        $sourceId = null;
        if (!empty($validated['source_code'])) {
            $source = SourceReadModel::where('code', $validated['source_code'])->first();
            if ($source) $sourceId = $source->id;
        }

        $campaignId = null;
        if (!empty($validated['campaign_code'])) {
            $campaign = CampaignReadModel::where('code', $validated['campaign_code'])->first();
            if ($campaign) $campaignId = $campaign->id;
        }

        $interestTypeId = null;
        if (!empty($validated['interest_type_code'])) {
            $interestType = InterestTypeReadModel::where('code', $validated['interest_type_code'])->first();
            if ($interestType) $interestTypeId = $interestType->id;
        }

        $command = new CreateLeadCommand(
            $validated['name'],
            $validated['phone'],
            $validated['email'] ?? null,
            $centerId,
            $validated['dob'] ?? null,
            $sourceId,
            $campaignId,
            $interestTypeId,
            null // Webhooks don't usually assign specific users initially, rely on auto-assign later or keep unassigned
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
