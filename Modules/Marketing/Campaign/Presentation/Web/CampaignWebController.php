<?php

declare(strict_types=1);

namespace Modules\Marketing\Campaign\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Marketing\Campaign\Application\Commands\CreateCampaignCommand;
use Modules\Marketing\Campaign\Application\Commands\CreateCampaignHandler;
use Modules\Marketing\Campaign\Application\Commands\UpdateCampaignCommand;
use Modules\Marketing\Campaign\Application\Commands\UpdateCampaignHandler;
use Modules\Marketing\Campaign\Application\Commands\DeleteCampaignCommand;
use Modules\Marketing\Campaign\Application\Commands\DeleteCampaignHandler;
use Modules\Marketing\Campaign\Application\Queries\GetCampaignsQuery;
use Modules\Marketing\Campaign\Application\Queries\GetCampaignsHandler;
use Modules\Core\Center\Application\Queries\GetActiveCentersQuery;
use Modules\Core\Center\Application\Queries\GetActiveCentersHandler;
use App\Core\Helpers\PaginationHelper;
use Modules\Marketing\Campaign\Presentation\Web\Requests\StoreCampaignRequest;
use Modules\Marketing\Campaign\Presentation\Web\Requests\UpdateCampaignRequest;

class CampaignWebController extends Controller
{
    public function index(Request $request, GetCampaignsHandler $handler, GetActiveCentersHandler $centersHandler)
    {
        $perPage = PaginationHelper::resolvePerPage((int) $request->query('per_page'));
        $page = (int) $request->query('page', 1);
        $search = $request->query('search');
        $sortBy = $request->query('sort_by');
        $sortDir = PaginationHelper::resolveSortDirection($request->query('sort_dir'));
        
        $budgetFrom = $request->query('budget_from') !== null ? (float) $request->query('budget_from') : null;
        $budgetTo = $request->query('budget_to') !== null ? (float) $request->query('budget_to') : null;
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $isActive = $request->query('is_active') !== null ? ($request->query('is_active') === '1') : null;
        $centerId = $request->query('center_id');

        $query = new GetCampaignsQuery(
            $search, 
            $isActive, 
            $perPage, 
            $page, 
            $sortBy, 
            $sortDir, 
            $budgetFrom, 
            $budgetTo, 
            $dateFrom, 
            $dateTo, 
            $centerId
        );
        $campaigns = $handler->handle($query);

        $centers = $centersHandler->handle(new GetActiveCentersQuery());

        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        return view('campaign::index', compact(
            'campaigns', 'search', 'centers', 'isGlobalScope',
            'sortBy', 'sortDir', 'perPage',
            'budgetFrom', 'budgetTo', 'dateFrom', 'dateTo', 'isActive', 'centerId'
        ));
    }

    public function store(StoreCampaignRequest $request, CreateCampaignHandler $handler)
    {
        $validated = $request->getValidatedData();

        $command = new CreateCampaignCommand(
            $validated['name'],
            $validated['code'] ?? null,
            $validated['channel'] ?? null,
            $validated['budget'] ? (float)$validated['budget'] : null,
            $validated['center_id'],
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

    public function update(UpdateCampaignRequest $request, string $id, UpdateCampaignHandler $handler)
    {
        $validated = $request->getValidatedData();

        try {
            $command = new UpdateCampaignCommand(
                $id,
                $validated['name'],
                $validated['code'] ?? null,
                $validated['channel'] ?? null,
                $validated['budget'] ? (float)$validated['budget'] : null,
                $validated['center_id'],
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

