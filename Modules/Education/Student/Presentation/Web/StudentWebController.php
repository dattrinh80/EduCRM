<?php

declare(strict_types=1);

namespace Modules\Education\Student\Presentation\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Core\Helpers\PaginationHelper;
use Modules\Education\Student\Application\Queries\GetStudentsPaginatedQuery;
use Modules\Education\Student\Application\Queries\GetStudentsPaginatedHandler;
use Modules\Education\Student\Application\Queries\GetStudentByIdQuery;
use Modules\Education\Student\Application\Queries\GetStudentByIdHandler;
use Modules\Education\Student\Application\Commands\CreateStudentCommand;
use Modules\Education\Student\Application\Commands\CreateStudentHandler;
use Modules\Education\Student\Application\Commands\UpdateStudentCommand;
use Modules\Education\Student\Application\Commands\UpdateStudentHandler;
use Modules\Education\Student\Application\Commands\DeleteStudentCommand;
use Modules\Education\Student\Application\Commands\DeleteStudentHandler;

use Modules\Education\Student\Application\Queries\ExportStudentsQuery;
use Modules\Education\Student\Application\Queries\ExportStudentsHandler;
use Maatwebsite\Excel\Facades\Excel;

use Modules\Education\Student\Application\Commands\InitiateStudentImportCommand;
use Modules\Education\Student\Application\Commands\InitiateStudentImportHandler;
use Modules\Education\Student\Application\Commands\ProcessStudentImportChunkCommand;
use Modules\Education\Student\Application\Commands\ProcessStudentImportChunkHandler;

use Modules\Core\Center\Application\Queries\GetActiveCentersQuery;
use Modules\Core\Center\Application\Queries\GetActiveCentersHandler;

class StudentWebController extends Controller
{
    public function index(
        Request $request, 
        GetStudentsPaginatedHandler $handler,
        GetActiveCentersHandler $centersHandler
    ) {
        $perPage = PaginationHelper::resolvePerPage((int) $request->get('per_page'));
        $page = (int) $request->get('page', 1);
        $search = $request->get('search');
        $status = $request->get('status');
        $sortBy = $request->get('sort_by');
        $sortDir = PaginationHelper::resolveSortDirection($request->get('sort_direction'));

        $query = new GetStudentsPaginatedQuery(
            perPage: $perPage,
            page: $page,
            search: $search,
            status: $status,
            sortBy: $sortBy,
            sortDirection: $sortDir
        );

        $students = $handler->handle($query);
        $centers = $centersHandler->handle(new GetActiveCentersQuery());

        $isGlobalScope = false;
        try { $isGlobalScope = app('is_global_scope'); } catch (\Exception $e) {}

        return view('student::index', compact('students', 'centers', 'isGlobalScope'));
    }

    public function export(Request $request, ExportStudentsHandler $handler)
    {
        $format = $request->get('format', 'xlsx');
        $query = new ExportStudentsQuery(
            search: $request->get('search'),
            status: $request->get('status'),
            format: $format
        );

        $result = $handler->handle($query);

        if ($format === 'pdf') {
            return $result->download('danh-sach-hoc-vien-' . date('Y-m-d') . '.pdf');
        }

        return Excel::download($result, 'danh-sach-hoc-vien-' . date('Y-m-d') . '.' . $format);
    }

    public function import(Request $request, InitiateStudentImportHandler $handler)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $result = $handler->handle(new InitiateStudentImportCommand($request->file('file')));
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function importProcess(Request $request, ProcessStudentImportChunkHandler $handler)
    {
        $request->validate([
            'import_id' => 'required|string',
            'offset' => 'required|integer',
            'center_id' => 'required|string',
        ]);

        try {
            $command = new ProcessStudentImportChunkCommand(
                importId: $request->import_id,
                offset: (int) $request->offset,
                centerId: $request->center_id
            );

            $result = $handler->handle($command);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function create(GetActiveCentersHandler $centersHandler)
    {
        $centers = $centersHandler->handle(new GetActiveCentersQuery());
        return view('student::create', compact('centers'));
    }

    public function store(Request $request, CreateStudentHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'center_id' => 'required|string',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|in:MALE,FEMALE,OTHER',
            'address' => 'nullable|string|max:500',
            'guardians' => 'nullable|array',
            'guardians.*.name' => 'required|string|max:255',
            'guardians.*.phone' => 'required|string|max:20',
            'guardians.*.relationship' => 'required|string|max:50',
            'guardians.*.is_primary' => 'boolean',
        ]);

        try {
            $command = new CreateStudentCommand(
                name: $validated['name'],
                phone: $validated['phone'],
                email: $validated['email'],
                centerId: $validated['center_id'],
                dob: $validated['dob'],
                gender: $validated['gender'],
                address: $validated['address'],
                guardians: $validated['guardians'] ?? []
            );

            $handler->handle($command);

            return redirect()->route('admin.students.index')->with('success', 'Tạo học viên thành công.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function show(string $id, GetStudentByIdHandler $handler)
    {
        $student = $handler->handle(new GetStudentByIdQuery($id));
        if (!$student) {
            return redirect()->route('admin.students.index')->with('error', 'Không tìm thấy học viên.');
        }

        return view('student::show', compact('student'));
    }

    public function edit(string $id, GetStudentByIdHandler $handler, GetActiveCentersHandler $centersHandler)
    {
        $student = $handler->handle(new GetStudentByIdQuery($id));
        if (!$student) {
            return redirect()->route('admin.students.index')->with('error', 'Không tìm thấy học viên.');
        }

        $centers = $centersHandler->handle(new GetActiveCentersQuery());
        return view('student::edit', compact('student', 'centers'));
    }

    public function update(Request $request, string $id, UpdateStudentHandler $handler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|string',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|in:MALE,FEMALE,OTHER',
            'address' => 'nullable|string|max:500',
        ]);

        try {
            $command = new UpdateStudentCommand(
                id: $id,
                name: $validated['name'],
                phone: $validated['phone'],
                email: $validated['email'],
                status: $validated['status'],
                dob: $validated['dob'],
                gender: $validated['gender'],
                address: $validated['address']
            );

            $handler->handle($command);

            return redirect()->route('admin.students.index')->with('success', 'Cập nhật học viên thành công.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(string $id, DeleteStudentHandler $handler)
    {
        try {
            $handler->handle(new DeleteStudentCommand($id));
            return redirect()->route('admin.students.index')->with('success', 'Xóa học viên thành công.');
        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
