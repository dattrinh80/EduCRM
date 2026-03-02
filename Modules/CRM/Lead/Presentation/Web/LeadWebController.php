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
use Modules\CRM\Source\Application\Queries\GetSourcesQuery;
use Modules\CRM\Source\Application\Queries\GetSourcesHandler;
use Modules\CRM\InterestType\Application\Queries\GetInterestTypesQuery;
use Modules\CRM\InterestType\Application\Queries\GetInterestTypesHandler;
use Modules\Core\User\Infrastructure\ReadModels\UserReadModel;

class LeadWebController extends Controller
{
    public function index(
        Request $request, 
        GetLeadsPaginatedHandler $handler, 
        GetActiveCentersHandler $centersHandler,
        GetSourcesHandler $sourcesHandler,
        GetInterestTypesHandler $interestTypesHandler
    ) {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $query = new GetLeadsPaginatedQuery($perPage, $page);
        $leads = $handler->handle($query);

        $centers = $centersHandler->handle(new GetActiveCentersQuery());
        $sources = $sourcesHandler->handle(new GetSourcesQuery(null, true));
        $interestTypes = $interestTypesHandler->handle(new GetInterestTypesQuery(null, true));
        $users = UserReadModel::all(); // Assuming we use Eloquent read model directly for simplicity, or we should use a GetUsersQuery if available. For sales reps.

        return view('lead::index', compact('leads', 'centers', 'sources', 'interestTypes', 'users'));
    }

    public function store(Request $request, CreateLeadHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'center_id' => 'required|uuid|exists:centers,id',
            'dob' => 'nullable|date',
            'source_id' => 'nullable|uuid|exists:sources,id',
            'interest_type_id' => 'nullable|uuid|exists:interest_types,id',
            'assigned_to' => 'nullable|uuid|exists:users,id',
            'campaign_id' => 'nullable|uuid' // Campaign not implemented yet
        ]);

        $command = new CreateLeadCommand(
            $validated['name'],
            $validated['phone'],
            $validated['email'] ?? null,
            $validated['center_id'] ?? null,
            $validated['dob'] ?? null,
            $validated['source_id'] ?? null,
            $validated['campaign_id'] ?? null,
            $validated['interest_type_id'] ?? null,
            $validated['assigned_to'] ?? null
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
            'center_id' => 'required|uuid|exists:centers,id',
            'dob' => 'nullable|date',
            'source_id' => 'nullable|uuid|exists:sources,id',
            'interest_type_id' => 'nullable|uuid|exists:interest_types,id',
            'assigned_to' => 'nullable|uuid|exists:users,id',
            'campaign_id' => 'nullable|uuid'
        ]);

        try {
            $command = new UpdateLeadCommand(
                $id,
                $validated['name'],
                $validated['phone'],
                $validated['status'],
                $validated['email'] ?? null,
                $validated['center_id'] ?? null,
                $validated['dob'] ?? null,
                $validated['source_id'] ?? null,
                $validated['campaign_id'] ?? null,
                $validated['interest_type_id'] ?? null,
                $validated['assigned_to'] ?? null
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
