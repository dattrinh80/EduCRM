<?php

declare(strict_types=1);

namespace Modules\Core\Center\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetCentersPaginatedHandler implements QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        /** @var GetCentersPaginatedQuery $query */

        return CenterReadModel::query()
            ->latest()
            ->paginate($query->perPage, ['*'], 'page', $query->page);
    }
}
