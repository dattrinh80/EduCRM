<?php

declare(strict_types=1);

namespace Modules\CRM\Lead\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\CRM\Lead\Infrastructure\ReadModels\LeadReadModel;
use Modules\Core\Center\Application\Queries\GetActiveCentersHandler;
use Modules\Core\Center\Application\Queries\GetActiveCentersQuery;
use Modules\Core\User\Application\Queries\GetAllUsersHandler;
use Modules\Core\User\Application\Queries\GetAllUsersQuery;
use Modules\CRM\Lead\Application\Exports\LeadsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;

class ExportLeadsHandler implements QueryHandler
{
    public function __construct(
        private readonly GetActiveCentersHandler $centersHandler,
        private readonly GetAllUsersHandler $usersHandler
    ) {}

    public function handle(Query $query): mixed
    {
        /** @var ExportLeadsQuery $query */
        $builder = LeadReadModel::query();

        if ($query->search) {
            $builder->where('name', 'like', "%{$query->search}%");
        }

        if ($query->phone) {
            $builder->where('phone', 'like', "%{$query->phone}%");
        }

        if ($query->centerId) {
            $builder->where('center_id', $query->centerId);
        }

        if ($query->statusId) {
            $builder->where('status_id', $query->statusId);
        }

        // Batch fetching leads to avoid memory issues (though we collect them for the final export)
        $allLeads = new Collection();
        $builder->with(['leadSource', 'interestType', 'leadStatus', 'center', 'assignTo', 'campaign'])
                ->chunk(1000, function ($chunk) use (&$allLeads) {
                    $allLeads = $allLeads->merge($chunk);
                });

        $centers = $this->centersHandler->handle(new GetActiveCentersQuery())->pluck('name', 'id')->toArray();
        $users = $this->usersHandler->handle(new GetAllUsersQuery())->pluck('name', 'id')->toArray();

        if ($query->format === 'pdf') {
            return Pdf::loadView('lead::exports.pdf', [
                'leads' => $allLeads,
                'centers' => $centers,
                'users' => $users
            ])->setPaper('a4', 'landscape');
        }

        return new LeadsExport($allLeads, $centers, $users);
    }
}
