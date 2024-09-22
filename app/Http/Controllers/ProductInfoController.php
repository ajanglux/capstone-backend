<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductInfoRequest;
use App\Models\ProductInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductInfoController extends Controller
{
    public function index(): JsonResponse
    {
        $productInfos = ProductInfo::all();
        return response()->json($productInfos, 200);
    }

    public function show($id): JsonResponse
    {
        try {
            $productInfo = ProductInfo::findOrFail($id);
            return response()->json($productInfo, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'ProductInfo not found'], 404);
        }
    }

    public function store(ProductInfoRequest $request): JsonResponse
    {
        try {
            $productInfo = ProductInfo::create($request->validated());
            return response()->json($productInfo, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create ProductInfo', 'details' => $e->getMessage()], 500);
        }
    }

    public function update(ProductInfoRequest $request, $id): JsonResponse
    {
        try {
            $productInfo = ProductInfo::findOrFail($id);
            $productInfo->update($request->validated());

            return response()->json($productInfo, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'ProductInfo not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update ProductInfo', 'details' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $productInfo = ProductInfo::findOrFail($id);
            $productInfo->delete();
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'ProductInfo not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete ProductInfo', 'details' => $e->getMessage()], 500);
        }
    }
}
