<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Lead\Application\Commands\CreateLeadCommand;
use Modules\CRM\Lead\Application\Commands\CreateLeadHandler;
use Modules\CRM\Lead\Application\Commands\UpdateLeadCommand;
use Modules\CRM\Lead\Application\Commands\UpdateLeadHandler;
use Modules\CRM\Lead\Application\Commands\DeleteLeadCommand;
use Modules\CRM\Lead\Application\Commands\DeleteLeadHandler;
use Modules\CRM\Lead\Application\Queries\GetLeadByIdQuery;
use Modules\CRM\Lead\Application\Queries\GetLeadByIdHandler;
use Modules\CRM\Lead\Application\Queries\GetLeadsPaginatedQuery;
use Modules\CRM\Lead\Application\Queries\GetLeadsPaginatedHandler;
use Modules\Core\Center\Application\Queries\GetActiveCentersQuery;
use Modules\Core\Center\Application\Queries\GetActiveCentersHandler;

class LeadWebController extends Controller
{
    public function index(Request $request, GetLeadsPaginatedHandler $handler, GetActiveCentersHandler $centersHandler)
    {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $query = new GetLeadsPaginatedQuery($perPage, $page);
        $leads = $handler->handle($query);

        $centers = $centersHandler->handle(new GetActiveCentersQuery());

        return view('lead::index', compact('leads', 'centers'));
    }

    public function store(Request $request, CreateLeadHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'center_id' => 'required|uuid|exists:centers,id'
        ]);

        $command = new CreateLeadCommand(
            $validated['name'],
            $validated['phone'],
            $validated['email'] ?? null,
            $validated['center_id'] ?? null
        );

        $handler->handle($command);

        return redirect()->route('admin.leads.index')->with('success', 'Lead created successfully.');
    }



    public function update(Request $request, string $id, UpdateLeadHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'center_id' => 'required|uuid|exists:centers,id'
        ]);

        try {
            $command = new UpdateLeadCommand(
                $id,
                $validated['name'],
                $validated['phone'],
                $validated['status'],
                $validated['email'] ?? null,
                $validated['center_id'] ?? null
            );

            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.leads.index')->with('error', 'Lead not found.');
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(string $id, DeleteLeadHandler $handler)
    {
        try {
            $command = new DeleteLeadCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.leads.index')->with('error', 'Lead not found.');
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted successfully.');
    }
}
