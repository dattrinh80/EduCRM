<?php

declare(strict_types=1);

namespace Modules\CRM\Campaign\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Campaign\Application\Commands\CreateCampaignCommand;
use Modules\CRM\Campaign\Application\Commands\CreateCampaignHandler;
use Modules\CRM\Campaign\Application\Commands\UpdateCampaignCommand;
use Modules\CRM\Campaign\Application\Commands\UpdateCampaignHandler;
use Modules\CRM\Campaign\Application\Commands\DeleteCampaignCommand;
use Modules\CRM\Campaign\Application\Commands\DeleteCampaignHandler;
use Modules\CRM\Campaign\Application\Queries\GetCampaignsQuery;
use Modules\CRM\Campaign\Application\Queries\GetCampaignsHandler;
use Modules\Core\Center\Application\Queries\GetActiveCentersQuery;
use Modules\Core\Center\Application\Queries\GetActiveCentersHandler;

class CampaignWebController extends Controller
{
    public function index(Request $request, GetCampaignsHandler $handler, GetActiveCentersHandler $centersHandler)
    {
        $search = $request->query('search');
        
        $query = new GetCampaignsQuery($search);
        $campaigns = $handler->handle($query);

        $centers = $centersHandler->handle(new GetActiveCentersQuery());

        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        return view('campaign::index', compact('campaigns', 'search', 'centers', 'isGlobalScope'));
    }

    public function store(Request $request, CreateCampaignHandler $handler)
    {
        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:campaigns,code',
            'channel' => 'nullable|string|max:100',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];

        if ($isGlobalScope) {
            $rules['center_id'] = 'required|uuid|exists:centers,id';
        }

        $validated = $request->validate($rules);

        $centerId = $isGlobalScope
            ? ($validated['center_id'] ?? null)
            : (session('current_center_id') ?? app('center_id'));

        $command = new CreateCampaignCommand(
            $validated['name'],
            $validated['code'] ?? null,
            $validated['channel'] ?? null,
            $validated['budget'] ? (float)$validated['budget'] : null,
            $centerId,
            $validated['start_date'] ? new \DateTimeImmutable($validated['start_date']) : null,
            $validated['end_date'] ? new \DateTimeImmutable($validated['end_date']) : null
        );

        try {
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.campaigns.index')->with('success', 'Chiến dịch được tạo thành công.');
    }

    public function update(Request $request, string $id, UpdateCampaignHandler $handler)
    {
        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:campaigns,code,' . $id,
            'channel' => 'nullable|string|max:100',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'required|boolean'
        ];

        if ($isGlobalScope) {
            $rules['center_id'] = 'required|uuid|exists:centers,id';
        }

        $validated = $request->validate($rules);

        $centerId = $isGlobalScope
            ? ($validated['center_id'] ?? null)
            : (session('current_center_id') ?? app('center_id'));

        try {
            $command = new UpdateCampaignCommand(
                $id,
                $validated['name'],
                $validated['code'] ?? null,
                $validated['channel'] ?? null,
                $validated['budget'] ? (float)$validated['budget'] : null,
                $centerId,
                $validated['start_date'] ? new \DateTimeImmutable($validated['start_date']) : null,
                $validated['end_date'] ? new \DateTimeImmutable($validated['end_date']) : null,
                (bool) $validated['is_active']
            );

            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.campaigns.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.campaigns.index')->with('success', 'Cập nhật chiến dịch thành công.');
    }

    public function destroy(string $id, DeleteCampaignHandler $handler)
    {
        try {
            $command = new DeleteCampaignCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.campaigns.index')->with('error', 'Không tìm thấy chiến dịch.');
        }

        return redirect()->route('admin.campaigns.index')->with('success', 'Xoá chiến dịch thành công.');
    }
}
