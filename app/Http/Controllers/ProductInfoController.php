<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductInfoRequest;
use App\Models\ProductInfo;
use Illuminate\Http\Request;

class ProductInfoController extends Controller
{
    public function index()
    {
        $productInfos = ProductInfo::all();
        return response()->json($productInfos, 200);
    }

    public function store(ProductInfoRequest $request)
    {
        $productInfo = ProductInfo::create($request->validated());
        return response()->json($productInfo, 201);
    }

    public function show($id)
    {
        $productInfo = ProductInfo::findOrFail($id);
        return response()->json($productInfo, 200);
    }

    public function update(ProductInfoRequest $request, $id)
    {
        $productInfo = ProductInfo::findOrFail($id);
        $productInfo->update($request->validated());
        return response()->json($productInfo, 200);
    }

    public function destroy($id)
    {
        $productInfo = ProductInfo::findOrFail($id);
        $productInfo->delete();
        return response()->json(null, 204);
    }
}
