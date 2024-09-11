<?php

namespace App\Repositories;

use App\Models\CustomerDetail;
use App\Interfaces\CustomerDetailInterface;

class CustomerDetailRepository implements CustomerDetailInterface
{
    // Retrieve all customer details and convert them to an array
    public function getAll(): array
    {
        return CustomerDetail::all()->toArray(); // Convert collection to array
    }

    public function create(array $data): object|null
    {
        return CustomerDetail::create($data);
    }

    public function getById(int $id): object|null
    {
        return CustomerDetail::find($id);
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
