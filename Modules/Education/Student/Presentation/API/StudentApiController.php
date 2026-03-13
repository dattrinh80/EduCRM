<?php

declare(strict_types=1);

namespace Modules\Education\Student\Presentation\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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

class StudentApiController extends Controller
{
    public function index(Request $request, GetStudentsPaginatedHandler $handler)
    {
        $query = new GetStudentsPaginatedQuery(
            perPage: (int) $request->get('per_page', 15),
            page: (int) $request->get('page', 1),
            search: $request->get('search'),
            status: $request->get('status'),
            sortBy: $request->get('sort_by'),
            sortDirection: $request->get('sort_direction')
        );

        return response()->json($handler->handle($query));
    }

    public function show(string $id, GetStudentByIdHandler $handler)
    {
        $student = $handler->handle(new GetStudentByIdQuery($id));
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        return response()->json($student);
    }

    public function store(Request $request, CreateStudentHandler $handler)
    {
        $command = new CreateStudentCommand(
            name: $request->name,
            phone: $request->phone,
            email: $request->email,
            centerId: $request->center_id,
            dob: $request->dob,
            gender: $request->gender,
            address: $request->address,
            guardians: $request->guardians ?? []
        );

        $student = $handler->handle($command);
        return response()->json($student, 201);
    }

    public function update(Request $request, string $id, UpdateStudentHandler $handler)
    {
        $command = new UpdateStudentCommand(
            id: $id,
            name: $request->name,
            phone: $request->phone,
            email: $request->email,
            status: $request->status,
            dob: $request->dob,
            gender: $request->gender,
            address: $request->address
        );

        $student = $handler->handle($command);
        return response()->json($student);
    }

    public function destroy(string $id, DeleteStudentHandler $handler)
    {
        $handler->handle(new DeleteStudentCommand($id));
        return response()->json(null, 204);
    }
}
