<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Presentation\API;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Customer\Application\Queries\GetCustomersPaginatedQuery;
use Modules\CRM\Customer\Application\Queries\GetCustomersPaginatedHandler;
use Modules\CRM\Customer\Application\Commands\CreateCustomerCommand;
use Modules\CRM\Customer\Application\Commands\CreateCustomerHandler;
use Modules\CRM\Customer\Application\Commands\UpdateCustomerCommand;
use Modules\CRM\Customer\Application\Commands\UpdateCustomerHandler;
use Modules\CRM\Customer\Application\Commands\DeleteCustomerCommand;
use Modules\CRM\Customer\Application\Commands\DeleteCustomerHandler;
use Illuminate\Http\JsonResponse;

class CustomerApiController extends Controller
{
    public function index(Request $request, GetCustomersPaginatedHandler $handler): JsonResponse
    {
        $query = new GetCustomersPaginatedQuery(
            perPage: (int) $request->query('per_page', 15),
            page: (int) $request->query('page', 1),
            search: $request->query('search'),
            phone: $request->query('phone'),
            centerId: $request->query('center_id'),
            sortBy: $request->query('sort_by'),
            sortDir: $request->query('sort_dir', 'desc')
        );

        $customers = $handler->handle($query);

        return response()->json($customers);
    }

    public function store(Request $request, CreateCustomerHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'center_id' => 'nullable|uuid',
        ]);

        $command = new CreateCustomerCommand(
            $validated['name'],
            $validated['phone'] ?? null,
            $validated['email'] ?? null,
            null, null, null,
            $validated['center_id'] ?? null
        );

        $handler->handle($command);

        return response()->json(['message' => 'Customer created successfully'], 201);
    }

    public function show(string $id, \Modules\CRM\Customer\Application\Queries\GetCustomerByIdHandler $handler): JsonResponse
    {
        $customer = $handler->handle(new \Modules\CRM\Customer\Application\Queries\GetCustomerByIdQuery($id));

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json($customer);
    }

    public function update(Request $request, string $id, UpdateCustomerHandler $handler): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $command = new UpdateCustomerCommand(
            $id,
            $validated['name'],
            $validated['phone'] ?? null
        );

        $handler->handle($command);

        return response()->json(['message' => 'Customer updated successfully']);
    }

    public function destroy(string $id, DeleteCustomerHandler $handler): JsonResponse
    {
        $handler->handle(new DeleteCustomerCommand($id));

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
