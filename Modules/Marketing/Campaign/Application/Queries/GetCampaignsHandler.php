<?php
declare(strict_types=1);
namespace Modules\Marketing\Campaign\Application\Queries;
use Modules\Marketing\Campaign\Infrastructure\ReadModels\CampaignReadModel;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Helpers\PaginationHelper;
class GetCampaignsHandler
{
    public function handle(GetCampaignsQuery $query): LengthAwarePaginator
    {
        $dbQuery = CampaignReadModel::query();
        if ($query->search) {
            $dbQuery->where(function ($q) use ($query) {
                $q->where("name", "like", "%" . $query->search . "%")
                  ->orWhere("code", "like", "%" . $query->search . "%");
            });
        }
        if ($query->isActive !== null) {
            $dbQuery->where("is_active", $query->isActive);
        }

        if ($query->budgetFrom !== null) {
            $dbQuery->where('budget', '>=', $query->budgetFrom);
        }
        if ($query->budgetTo !== null) {
            $dbQuery->where('budget', '<=', $query->budgetTo);
        }

        if ($query->dateFrom !== null) {
            $dbQuery->where('start_date', '>=', $query->dateFrom);
        }
        if ($query->dateTo !== null) {
            $dbQuery->where('end_date', '<=', $query->dateTo);
        }

        if ($query->centerId !== null) {
            $dbQuery->where('center_id', $query->centerId);
        }

        // Apply sorting
        $sortableColumns = config('crm.campaign.sortable_columns', ['name', 'code', 'created_at']);
        $validSortColumn = PaginationHelper::resolveSortColumn($query->sortBy, $sortableColumns);
        
        if ($validSortColumn) {
            $direction = PaginationHelper::resolveSortDirection($query->sortDirection);
            $dbQuery->orderBy($validSortColumn, $direction);
        } else {
            $dbQuery->orderBy("created_at", "desc");
        }

        return $dbQuery->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}