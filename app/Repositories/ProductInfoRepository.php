<?php

namespace App\Repositories;

use App\Interfaces\ProductInfoInterface;
use App\Models\ProductInfo;

class ProductInfoRepository implements ProductInfoInterface
{
    public function getAll()
    {
        return ProductInfo::all();
    }

    public function getById($id)
    {
        return ProductInfo::findOrFail($id);
    }

    public function create(array $data)
    {
        return ProductInfo::create($data);
    }

    public function update($id, array $data)
    {
        $productInfo = ProductInfo::findOrFail($id);
        $productInfo->update($data);
        return $productInfo;
    }

    public function delete($id)
    {
        $productInfo = ProductInfo::findOrFail($id);
        $productInfo->delete();
        return true;
    }
}
