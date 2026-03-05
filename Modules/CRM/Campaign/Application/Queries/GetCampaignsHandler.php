<?php
declare(strict_types=1);
namespace Modules\CRM\Campaign\Application\Queries;
use Modules\CRM\Campaign\Infrastructure\ReadModels\CampaignReadModel;
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