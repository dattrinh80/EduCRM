<?php

declare(strict_types=1);

namespace Modules\Education\Student\Presentation\Web;

use Illuminate\Routing\Controller;
use Modules\Education\Student\Application\Queries\GetStudentsQuery;
use Modules\Education\Student\Application\Queries\GetStudentsHandler;

class StudentWebController extends Controller
{
    public function index(GetStudentsHandler $handler)
    {
        $students = $handler->handle(new GetStudentsQuery());
        return view('student::index', compact('students'));
    }
}
