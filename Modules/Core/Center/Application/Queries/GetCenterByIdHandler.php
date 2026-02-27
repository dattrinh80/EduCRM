<?php

declare(strict_types=1);

namespace Modules\Core\Center\Application\Queries;

use App\Core\CQRS\QueryHandler;
use App\Core\CQRS\Query;
use Modules\Core\Center\Infrastructure\ReadModels\CenterReadModel;

class GetCenterByIdHandler implements QueryHandler
{
    public function handle(Query $query): ?CenterReadModel
    {
        /** @var GetCenterByIdQuery $query */

        return CenterReadModel::find($query->id);
    }
}
