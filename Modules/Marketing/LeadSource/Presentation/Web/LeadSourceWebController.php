<?php

declare(strict_types=1);

namespace Modules\Marketing\LeadSource\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Marketing\LeadSource\Application\Commands\CreateLeadSourceCommand;
use Modules\Marketing\LeadSource\Application\Commands\CreateLeadSourceHandler;
use Modules\Marketing\LeadSource\Application\Commands\UpdateLeadSourceCommand;
use Modules\Marketing\LeadSource\Application\Commands\UpdateLeadSourceHandler;
use Modules\Marketing\LeadSource\Application\Commands\DeleteLeadSourceCommand;
use Modules\Marketing\LeadSource\Application\Commands\DeleteLeadSourceHandler;
use Modules\Marketing\LeadSource\Application\Queries\GetLeadSourcesQuery;
use Modules\Marketing\LeadSource\Application\Queries\GetLeadSourcesHandler;

class LeadSourceWebController extends Controller
{
    public function index(Request $request, GetLeadSourcesHandler $handler)
    {
        $search = $request->query('search');
        
        $query = new GetLeadSourcesQuery($search);
        $leadSources = $handler->handle($query);

        return view('lead_source::index', compact('leadSources', 'search'));
    }

    public function store(Request $request, CreateLeadSourceHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:lead_sources,code',
        ]);

        $command = new CreateLeadSourceCommand(
            $validated['name'],
            $validated['code']
        );

        $handler->handle($command);

        return redirect()->route('admin.lead-sources.index')->with('success', 'Nguồn khách hàng được tạo thành công.');
    }

    public function update(Request $request, string $id, UpdateLeadSourceHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:lead_sources,code,' . $id,
            'is_active' => 'required|boolean'
        ]);

        try {
            $command = new UpdateLeadSourceCommand(
                $id,
                $validated['name'],
                $validated['code'],
                (bool) $validated['is_active']
            );

            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.lead-sources.index')->with('error', 'Không tìm thấy nguồn khách hàng.');
        }

        return redirect()->route('admin.lead-sources.index')->with('success', 'Cập nhật nguồn khách hàng thành công.');
    }

    public function destroy(string $id, DeleteLeadSourceHandler $handler)
    {
        try {
            $command = new DeleteLeadSourceCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.lead-sources.index')->with('error', 'Không tìm thấy nguồn khách hàng.');
        }

        return redirect()->route('admin.lead-sources.index')->with('success', 'Xoá nguồn khách hàng thành công.');
    }
}
