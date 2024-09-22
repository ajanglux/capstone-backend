<?php

namespace App\Repositories;

use App\Models\CustomerDetail;
use App\Interfaces\CustomerDetailInterface;

class CustomerDetailRepository implements CustomerDetailInterface
{
    public function getAll(): array
    {
        return CustomerDetail::all()->toArray();
    }

    public function create(array $data): object|null
    {
        return CustomerDetail::create($data);
    }

    public function getById(int $id): object|null
    {
        return CustomerDetail::with('productInfos')->find($id);
    }

    public function getByCode(string $code): object|null
    {
        return CustomerDetail::where('code', $code)->first();
    }

    public function update(int $id, array $data): object|null
    {
        $customerDetail = CustomerDetail::find($id);

        if ($customerDetail) {
            $customerDetail->update($data);
        }

        return $customerDetail;
    }

    public function delete(int $id): object|null
    {
        $customerDetail = CustomerDetail::find($id);

        if ($customerDetail) {
            $customerDetail->delete();
        }

        return $customerDetail;
    }
}