<?php

declare(strict_types=1);

namespace Modules\CRM\LeadTag\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\LeadTag\Application\Commands\CreateLeadTagCommand;
use Modules\CRM\LeadTag\Application\Commands\CreateLeadTagHandler;
use Modules\CRM\LeadTag\Application\Commands\UpdateLeadTagCommand;
use Modules\CRM\LeadTag\Application\Commands\UpdateLeadTagHandler;
use Modules\CRM\LeadTag\Application\Commands\DeleteLeadTagCommand;
use Modules\CRM\LeadTag\Application\Commands\DeleteLeadTagHandler;
use Modules\CRM\LeadTag\Application\Queries\GetLeadTagsQuery;
use Modules\CRM\LeadTag\Application\Queries\GetLeadTagsHandler;

class LeadTagWebController extends Controller
{
    public function index(Request $request, GetLeadTagsHandler $handler)
    {
        $search = $request->query('search');
        $query = new GetLeadTagsQuery($search);
        $tags = $handler->handle($query);

        return view('lead_tag::index', compact('tags', 'search'));
    }

    public function store(Request $request, CreateLeadTagHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        $handler->handle(new CreateLeadTagCommand(
            $validated['name'],
            $validated['color'] ?? 'slate'
        ));

        return redirect()->route('admin.lead-tags.index')->with('success', 'Nhãn được tạo thành công.');
    }

    public function update(Request $request, string $id, UpdateLeadTagHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        try {
            $handler->handle(new UpdateLeadTagCommand(
                $id,
                $validated['name'],
                $validated['color'] ?? 'slate'
            ));
        } catch (\Exception $e) {
            return redirect()->route('admin.lead-tags.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.lead-tags.index')->with('success', 'Cập nhật nhãn thành công.');
    }

    public function destroy(string $id, DeleteLeadTagHandler $handler)
    {
        try {
            $handler->handle(new DeleteLeadTagCommand($id));
        } catch (\Exception $e) {
            return redirect()->route('admin.lead-tags.index')->with('error', 'Không tìm thấy nhãn.');
        }

        return redirect()->route('admin.lead-tags.index')->with('success', 'Xoá nhãn thành công.');
    }
}
