<?php

declare(strict_types=1);

namespace Modules\CRM\LeadStatus\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\LeadStatus\Application\Commands\CreateLeadStatusCommand;
use Modules\CRM\LeadStatus\Application\Commands\CreateLeadStatusHandler;
use Modules\CRM\LeadStatus\Application\Commands\UpdateLeadStatusCommand;
use Modules\CRM\LeadStatus\Application\Commands\UpdateLeadStatusHandler;
use Modules\CRM\LeadStatus\Application\Commands\DeleteLeadStatusCommand;
use Modules\CRM\LeadStatus\Application\Commands\DeleteLeadStatusHandler;
use Modules\CRM\LeadStatus\Application\Queries\GetLeadStatusesQuery;
use Modules\CRM\LeadStatus\Application\Queries\GetLeadStatusesHandler;

class LeadStatusWebController extends Controller
{
    public function index(Request $request, GetLeadStatusesHandler $handler)
    {
        $search = $request->query('search');
        $query = new GetLeadStatusesQuery($search);
        $statuses = $handler->handle($query);

        return view('lead_status::index', compact('statuses', 'search'));
    }

    public function store(Request $request, CreateLeadStatusHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stage' => 'required|string|in:NEW,CONTACTED,INTERESTED,QUALIFIED,CONVERTED,LOST',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'color' => 'nullable|string|max:50',
        ]);

        $handler->handle(new CreateLeadStatusCommand(
            $validated['name'],
            $validated['stage'],
            (int) ($validated['sort_order'] ?? 0),
            (bool) $validated['is_active'],
            $validated['color'] ?? null
        ));

        return redirect()->route('admin.lead-statuses.index')->with('success', 'Trạng thái được tạo thành công.');
    }

    public function update(Request $request, string $id, UpdateLeadStatusHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stage' => 'required|string|in:NEW,CONTACTED,INTERESTED,QUALIFIED,CONVERTED,LOST',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'color' => 'nullable|string|max:50',
        ]);

        try {
            $handler->handle(new UpdateLeadStatusCommand(
                $id,
                $validated['name'],
                $validated['stage'],
                (int) ($validated['sort_order'] ?? 0),
                (bool) $validated['is_active'],
                $validated['color'] ?? null
            ));
        } catch (\Exception $e) {
            return redirect()->route('admin.lead-statuses.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.lead-statuses.index')->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function destroy(string $id, DeleteLeadStatusHandler $handler)
    {
        try {
            $handler->handle(new DeleteLeadStatusCommand($id));
        } catch (\Exception $e) {
            return redirect()->route('admin.lead-statuses.index')->with('error', 'Không tìm thấy trạng thái.');
        }

        return redirect()->route('admin.lead-statuses.index')->with('success', 'Xoá trạng thái thành công.');
    }
}
