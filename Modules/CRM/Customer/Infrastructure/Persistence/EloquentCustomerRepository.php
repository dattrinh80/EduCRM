<?php

declare(strict_types=1);

namespace Modules\CRM\Customer\Infrastructure\Persistence;

use Modules\CRM\Customer\Domain\Customer;
use Modules\CRM\Customer\Domain\CustomerRepositoryInterface;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findById(string $id): ?Customer
    {
        $model = CustomerModel::find($id);
        if (!$model) {
            return null;
        }

        return $this->mapToDomain($model);
    }

    public function findByPhone(string $phone): ?Customer
    {
        $model = CustomerModel::where('phone', $phone)->first();
        if (!$model) {
            return null;
        }

        return $this->mapToDomain($model);
    }

    public function findByEmail(string $email): ?Customer
    {
        $model = CustomerModel::where('email', $email)->first();
        if (!$model) {
            return null;
        }

        return $this->mapToDomain($model);
    }

    public function save(Customer $customer): void
    {
        CustomerModel::updateOrCreate(
            ['id' => $customer->id],
            [
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'dob' => $customer->dob,
                'gender' => $customer->gender,
                'address' => $customer->address,
                'center_id' => $customer->centerId,
            ]
        );
    }

    public function getAll(): array
    {
        return CustomerModel::latest()
            ->get()
            ->map(fn($model) => $this->mapToDomain($model))
            ->all();
    }

    public function search(?string $query = null): array
    {
        $eloquentQuery = CustomerModel::query();

        if ($query) {
            $eloquentQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });
        }

        return $eloquentQuery->latest()
            ->get()
            ->map(fn($model) => $this->mapToDomain($model))
            ->all();
    }


    private function mapToDomain(CustomerModel $model): Customer
    {
        return new Customer(
            id: $model->id,
            name: $model->name,
            phone: $model->phone,
            email: $model->email,
            dob: $model->dob,
            gender: $model->gender,
            address: $model->address,
            centerId: $model->center_id,
            createdAt: $model->created_at?->toDateTimeString(),
            updatedAt: $model->updated_at?->toDateTimeString()
        );
    }
}
