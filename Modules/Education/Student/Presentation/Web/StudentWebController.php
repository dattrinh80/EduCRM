<?php

declare(strict_types=1);

namespace Modules\Education\Student\Presentation\Web;

use Illuminate\Routing\Controller;
use Modules\Education\Student\Domain\StudentRepositoryInterface;

class StudentWebController extends Controller
{
    public function __construct(
        private StudentRepositoryInterface $studentRepository
    ) {}

    public function index()
    {
        $students = $this->studentRepository->getAll();
        return view('student::index', compact('students'));
    }
}
