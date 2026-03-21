<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\CustomerTag\Application\Commands\CreateCustomerTagCommand;
use Modules\CRM\CustomerTag\Application\Commands\CreateCustomerTagHandler;
use Modules\CRM\CustomerTag\Application\Commands\UpdateCustomerTagCommand;
use Modules\CRM\CustomerTag\Application\Commands\UpdateCustomerTagHandler;
use Modules\CRM\CustomerTag\Application\Commands\DeleteCustomerTagCommand;
use Modules\CRM\CustomerTag\Application\Commands\DeleteCustomerTagHandler;
use Modules\CRM\CustomerTag\Application\Queries\GetCustomerTagsQuery;
use Modules\CRM\CustomerTag\Application\Queries\GetCustomerTagsHandler;

class CustomerTagWebController extends Controller
{
    public function index(Request $request, GetCustomerTagsHandler $handler)
    {
        $search = $request->query('search');
        $query = new GetCustomerTagsQuery($search);
        $tags = $handler->handle($query);

        return view('customer_tag::index', compact('tags', 'search'));
    }

    public function create()
    {
        return view('customer_tag::partials.create_form');
    }

    public function edit(string $id, \Modules\CRM\CustomerTag\Domain\CustomerTagRepositoryInterface $repository)
    {
        $tag = $repository->findById($id);
        if (!$tag) {
            return response()->json(['error' => 'Customer tag not found'], 404);
        }
        return view('customer_tag::partials.edit_form', compact('tag'));
    }

    public function store(Request $request, CreateCustomerTagHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        $handler->handle(new CreateCustomerTagCommand(
            $validated['name'],
            $validated['color'] ?? 'slate'
        ));

        return redirect()->route('admin.customer-tags.index')->with('success', 'Nhãn được tạo thành công.');
    }

    public function update(Request $request, string $id, UpdateCustomerTagHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        try {
            $handler->handle(new UpdateCustomerTagCommand(
                $id,
                $validated['name'],
                $validated['color'] ?? 'slate'
            ));
        } catch (\Exception $e) {
            return redirect()->route('admin.customer-tags.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.customer-tags.index')->with('success', 'Cập nhật nhãn thành công.');
    }

    public function destroy(string $id, DeleteCustomerTagHandler $handler)
    {
        try {
            $handler->handle(new DeleteCustomerTagCommand($id));
        } catch (\Exception $e) {
            return redirect()->route('admin.customer-tags.index')->with('error', 'Không tìm thấy nhãn.');
        }

        return redirect()->route('admin.customer-tags.index')->with('success', 'Xoá nhãn thành công.');
    }
}
