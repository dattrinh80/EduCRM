<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Presentation\Web;

use Illuminate\Routing\Controller;
use Modules\CRM\Customer\Application\Queries\GetCustomersQuery;
use Modules\CRM\Customer\Application\Queries\GetCustomersHandler;
use Modules\CRM\Customer\Application\Commands\CreateCustomerCommand;
use Modules\CRM\Customer\Application\Commands\CreateCustomerHandler;
use Modules\CRM\Customer\Application\Commands\UpdateCustomerCommand;
use Modules\CRM\Customer\Application\Commands\UpdateCustomerHandler;
use Modules\Core\Center\Application\Queries\GetActiveCentersQuery;
use Modules\Core\Center\Application\Queries\GetActiveCentersHandler;
use Illuminate\Http\Request;

class CustomerWebController extends Controller
{
    public function index(
        Request $request, 
        GetCustomersHandler $handler,
        GetActiveCentersHandler $centersHandler
    ) {
        $search = $request->get('search');
        $customers = $handler->handle(new GetCustomersQuery($search));
        $centers = $centersHandler->handle(new GetActiveCentersQuery());
        
        return view('customer::index', compact('customers', 'search', 'centers'));
    }

    public function store(Request $request, CreateCustomerHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE,OTHER',
            'address' => 'nullable|string',
            'center_id' => 'nullable|uuid|exists:centers,id',
        ]);

        $command = new CreateCustomerCommand(
            $validated['name'],
            $validated['phone'],
            $validated['email'],
            $validated['dob'],
            $validated['gender'],
            $validated['address'],
            $validated['center_id']
        );

        $handler->handle($command);

        return redirect()->route('admin.customers.index')->with('success', 'Khách hàng đã được tạo.');
    }

    public function update(Request $request, string $id, UpdateCustomerHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE,OTHER',
            'address' => 'nullable|string',
            'center_id' => 'nullable|uuid|exists:centers,id',
        ]);

        $command = new UpdateCustomerCommand(
            $id,
            $validated['name'],
            $validated['phone'],
            $validated['email'],
            $validated['dob'],
            $validated['gender'],
            $validated['address'],
            $validated['center_id']
        );

        $handler->handle($command);

        return redirect()->route('admin.customers.index')->with('success', 'Khách hàng đã được cập nhật.');
    }
}
