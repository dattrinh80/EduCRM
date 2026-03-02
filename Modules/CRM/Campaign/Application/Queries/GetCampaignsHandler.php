<?php
declare(strict_types=1);
namespace Modules\CRM\Campaign\Application\Queries;
use Modules\CRM\Campaign\Infrastructure\ReadModels\CampaignReadModel;
use Illuminate\Pagination\LengthAwarePaginator;
class GetCampaignsHandler
{
    public function handle(GetCampaignsQuery $query): LengthAwarePaginator
    {
        $dbQuery = CampaignReadModel::query();
        if ($query->search) {
            $dbQuery->where("name", "like", "%" . $query->search . "%")
                     ->orWhere("code", "like", "%" . $query->search . "%");
        }
        if ($query->isActive !== null) {
            $dbQuery->where("is_active", $query->isActive);
        }
        return $dbQuery->orderBy("created_at", "desc")->paginate(15);
    }
}