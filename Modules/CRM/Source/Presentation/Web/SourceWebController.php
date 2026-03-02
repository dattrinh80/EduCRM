<?php

declare(strict_types=1);

namespace Modules\CRM\Source\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Source\Application\Commands\CreateSourceCommand;
use Modules\CRM\Source\Application\Commands\CreateSourceHandler;
use Modules\CRM\Source\Application\Commands\UpdateSourceCommand;
use Modules\CRM\Source\Application\Commands\UpdateSourceHandler;
use Modules\CRM\Source\Application\Commands\DeleteSourceCommand;
use Modules\CRM\Source\Application\Commands\DeleteSourceHandler;
use Modules\CRM\Source\Application\Queries\GetSourcesQuery;
use Modules\CRM\Source\Application\Queries\GetSourcesHandler;

class SourceWebController extends Controller
{
    public function index(Request $request, GetSourcesHandler $handler)
    {
        $search = $request->query('search');
        
        $query = new GetSourcesQuery($search);
        $sources = $handler->handle($query);

        return view('source::index', compact('sources', 'search'));
    }

    public function store(Request $request, CreateSourceHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:sources,code',
        ]);

        $command = new CreateSourceCommand(
            $validated['name'],
            $validated['code']
        );

        $handler->handle($command);

        return redirect()->route('admin.sources.index')->with('success', 'Nguồn khách hàng được tạo thành công.');
    }

    public function update(Request $request, string $id, UpdateSourceHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:sources,code,' . $id,
            'is_active' => 'required|boolean'
        ]);

        try {
            $command = new UpdateSourceCommand(
                $id,
                $validated['name'],
                $validated['code'],
                (bool) $validated['is_active']
            );

            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.sources.index')->with('error', 'Không tìm thấy nguồn khách hàng.');
        }

        return redirect()->route('admin.sources.index')->with('success', 'Cập nhật nguồn thành công.');
    }

    public function destroy(string $id, DeleteSourceHandler $handler)
    {
        try {
            $command = new DeleteSourceCommand($id);
            $handler->handle($command);
        } catch (\Exception $e) {
            return redirect()->route('admin.sources.index')->with('error', 'Không tìm thấy nguồn khách hàng.');
        }

        return redirect()->route('admin.sources.index')->with('success', 'Xoá nguồn thành công.');
    }
}
