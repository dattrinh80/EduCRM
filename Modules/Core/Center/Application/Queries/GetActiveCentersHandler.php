<?php

declare(strict_types=1);

namespace Modules\Core\Center\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel;
use Illuminate\Database\Eloquent\Collection;

class GetActiveCentersHandler implements QueryHandler
{
    public function handle(Query $query): Collection
    {
        /** @var GetActiveCentersQuery $query */

        return CenterReadModel::where('status', 'active')
            ->orderBy('name')
            ->get();
    }
}
