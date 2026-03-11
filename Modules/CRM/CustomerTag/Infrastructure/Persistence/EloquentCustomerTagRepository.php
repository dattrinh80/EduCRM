<?php

declare(strict_types=1);

namespace Modules\CRM\CustomerTag\Infrastructure\Persistence;

use Modules\CRM\CustomerTag\Domain\CustomerTag;
use Modules\CRM\CustomerTag\Domain\CustomerTagRepositoryInterface;
use Modules\CRM\CustomerTag\Infrastructure\ReadModels\CustomerTagReadModel;
use Illuminate\Support\Facades\DB;

class EloquentCustomerTagRepository implements CustomerTagRepositoryInterface
{
    public function save(CustomerTag $tag): void
    {
        CustomerTagReadModel::updateOrCreate(
            ['id' => $tag->getId()],
            [
                'name' => $tag->name,
                'color' => $tag->color,
            ]
        );
    }

    public function findById(string $id): ?CustomerTag
    {
        $model = CustomerTagReadModel::find($id);
        if (!$model) return null;

        return new CustomerTag($model->id, $model->name, $model->color);
    }

    public function findByName(string $name): ?CustomerTag
    {
        $model = CustomerTagReadModel::where('name', $name)->first();
        if (!$model) return null;

        return new CustomerTag($model->id, $model->name, $model->color);
    }

    public function getAll(): array
    {
        return CustomerTagReadModel::orderBy('name')
            ->get()
            ->map(fn($m) => new CustomerTag($m->id, $m->name, $m->color))
            ->toArray();
    }

    public function delete(string $id): void
    {
        CustomerTagReadModel::destroy($id);
    }

    public function syncTagsForCustomer(string $customerId, array $tagIds): void
    {
        DB::table('customer_tag_pivot')->where('customer_id', $customerId)->delete();
        
        if (empty($tagIds)) return;

        $data = array_map(fn($tagId) => [
            'customer_id' => $customerId,
            'tag_id' => $tagId
        ], $tagIds);

        DB::table('customer_tag_pivot')->insert($data);
    }
}

