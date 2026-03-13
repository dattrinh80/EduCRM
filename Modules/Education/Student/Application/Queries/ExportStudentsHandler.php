<?php

declare(strict_types=1);

namespace Modules\Education\Student\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Education\Student\Infrastructure\ReadModels\StudentReadModel;
use Modules\Education\Student\Application\Exports\StudentsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;

class ExportStudentsHandler implements QueryHandler
{
    public function handle(Query $query): mixed
    {
        /** @var ExportStudentsQuery $query */
        $builder = StudentReadModel::query()
            ->with(['customer', 'customer.center']);

        if (!empty($query->search)) {
            $builder->where(function ($q) use ($query) {
                $q->where('student_code', 'like', '%' . $query->search . '%')
                  ->orWhereHas('customer', function ($cq) use ($query) {
                      $cq->where('name', 'like', '%' . $query->search . '%')
                         ->orWhere('phone', 'like', '%' . $query->search . '%');
                  });
            });
        }

        if (!empty($query->status)) {
            $builder->where('status', $query->status);
        }

        $allStudents = new Collection();
        $builder->chunk(1000, function ($chunk) use (&$allStudents) {
            $allStudents = $allStudents->merge($chunk);
        });

        if ($query->format === 'pdf') {
            return Pdf::loadView('student::exports.pdf', [
                'students' => $allStudents
            ])->setPaper('a4', 'landscape');
        }

        return new StudentsExport($allStudents);
    }
}
