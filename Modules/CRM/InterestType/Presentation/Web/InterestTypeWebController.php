<?php

declare(strict_types=1);

namespace Modules\CRM\InterestType\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\InterestType\Application\Commands\CreateInterestTypeCommand;
use Modules\CRM\InterestType\Application\Commands\CreateInterestTypeHandler;
use Modules\CRM\InterestType\Application\Commands\UpdateInterestTypeCommand;
use Modules\CRM\InterestType\Application\Commands\UpdateInterestTypeHandler;
use Modules\CRM\InterestType\Application\Commands\DeleteInterestTypeCommand;
use Modules\CRM\InterestType\Application\Commands\DeleteInterestTypeHandler;
use Modules\CRM\InterestType\Application\Queries\GetInterestTypesQuery;
use Modules\CRM\InterestType\Application\Queries\GetInterestTypesHandler;

class InterestTypeWebController extends Controller
{
    public function index(Request $request, GetInterestTypesHandler $handler)
    {
        $search = $request->query('search');
        
        $query = new GetInterestTypesQuery($search);
        $interestTypes = $handler->handle($query);

        return view('interest-type::index', compact('interestTypes', 'search'));
    }

    public function store(Request $request, CreateInterestTypeHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $command = new CreateInterestTypeCommand(
            $validated['name'],
            $validated['description'] ?? null
        );

        $handler->handle($command);

        return redirect()->route('admin.interest-types.index')->with('success', 'Nhu cầu/Loại dịch vụ được tạo thành công.');
    }

    public function update(Request $request, string $id, UpdateInterestTypeHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean'
        ]);

        try {
            $command = new UpdateInterestTypeCommand(
                $id,
                $validated['name'],
                $validated['description'] ?? null,
                (bool) $validated['is_active']
            );

            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.interest-types.index')->with('error', 'Không tìm thấy thông tin.');
        }

        return redirect()->route('admin.interest-types.index')->with('success', 'Cập nhật thành công.');
    }

    public function destroy(string $id, DeleteInterestTypeHandler $handler)
    {
        try {
            $command = new DeleteInterestTypeCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.interest-types.index')->with('error', 'Không tìm thấy thông tin.');
        }

        return redirect()->route('admin.interest-types.index')->with('success', 'Xoá thành công.');
    }
}
