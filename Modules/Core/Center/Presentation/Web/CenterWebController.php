<?php

declare(strict_types=1);

namespace Modules\Core\Center\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Center\Application\Commands\CreateCenterCommand;
use Modules\Core\Center\Application\Commands\CreateCenterHandler;
use Modules\Core\Center\Application\Commands\UpdateCenterCommand;
use Modules\Core\Center\Application\Commands\UpdateCenterHandler;
use Modules\Core\Center\Application\Commands\DeleteCenterCommand;
use Modules\Core\Center\Application\Commands\DeleteCenterHandler;

use Modules\Core\Center\Application\Queries\GetCentersPaginatedQuery;
use Modules\Core\Center\Application\Queries\GetCentersPaginatedHandler;

class CenterWebController extends Controller
{
    public function index(Request $request, GetCentersPaginatedHandler $handler)
    {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $query = new GetCentersPaginatedQuery($perPage, $page);
        $centers = $handler->handle($query);

        return view('center::index', compact('centers'));
    }


    public function store(Request $request, CreateCenterHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:centers,code',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500'
        ]);

        $command = new CreateCenterCommand(
            $validated['name'],
            $validated['code'],
            $validated['phone'] ?? null,
            $validated['email'] ?? null,
            $validated['address'] ?? null
        );

        $handler->handle($command);

        return redirect()->route('admin.centers.index')->with('success', 'Tạo cơ sở mới thành công.');
    }


    public function update(Request $request, string $id, UpdateCenterHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:centers,code,' . $id,
            'status' => 'required|string|in:active,inactive',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500'
        ]);

        try {
            $command = new UpdateCenterCommand(
                $id,
                $validated['name'],
                $validated['code'],
                $validated['status'],
                $validated['phone'] ?? null,
                $validated['email'] ?? null,
                $validated['address'] ?? null
            );

            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.centers.index')->with('error', 'Không tìm thấy cơ sở.');
        }

        return redirect()->route('admin.centers.index')->with('success', 'Cập nhật cơ sở thành công.');
    }

    public function destroy(string $id, DeleteCenterHandler $handler)
    {
        try {
            $command = new DeleteCenterCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.centers.index')->with('error', 'Không tìm thấy cơ sở.');
        }

        return redirect()->route('admin.centers.index')->with('success', 'Xoá cơ sở thành công.');
    }
}
