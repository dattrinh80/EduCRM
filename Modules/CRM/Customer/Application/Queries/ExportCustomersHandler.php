<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Application\Queries;

use Modules\CRM\Customer\Infrastructure\ReadModels\CustomerReadModel;
use Modules\Core\Center\Application\Queries\GetActiveCentersHandler;
use Modules\Core\Center\Application\Queries\GetActiveCentersQuery;
use Modules\CRM\Customer\Application\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;

class ExportCustomersHandler
{
    public function __construct(
        private readonly GetActiveCentersHandler $centersHandler
    ) {}

    public function handle(ExportCustomersQuery $query)
    {
        $eloquent = CustomerReadModel::query();

        if ($query->search) {
            $eloquent->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query->search}%")
                  ->orWhere('email', 'like', "%{$query->search}%");
            });
        }

        if ($query->phone) {
            $eloquent->where('phone', 'like', "%{$query->phone}%");
        }

        if ($query->centerId) {
            $eloquent->where('center_id', $query->centerId);
        }

        $allCustomers = $eloquent->get();
        $centers = $this->centersHandler->handle(new GetActiveCentersQuery())->pluck('name', 'id')->toArray();

        if ($query->format === 'pdf') {
            $pdf = Pdf::loadView('customer::exports.pdf', [
                'customers' => $allCustomers,
                'centers' => $centers
            ])->setPaper('a4', 'landscape');
            
            return $pdf;
        }

        return new CustomersExport($allCustomers, $centers);
    }
}
