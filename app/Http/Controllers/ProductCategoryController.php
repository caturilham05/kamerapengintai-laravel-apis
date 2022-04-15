<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product_cat = ProductCategory::paginate(15);
        $count = ProductCategory::count();

        if ($count == 0) {
            $result = [
                'status' => 'error',
                'message' => 'No data found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }
        $result = [
            'status' => 'success',
            'message' => 'Product Category retrieved successfully.',
            'result' => $product_cat,
        ];
        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product_cat_detail = ProductCategory::select()
        ->where('id', $id)
        ->where('publish', 1)
        ->first();
        $count = ProductCategory::count();
        if (empty($product_cat_detail)) {
            $result = [
                'status' => 'error',
                'message' => 'Product Category not found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }
        $result = [
            'status' => 'success',
            'message' => 'Product Category retrieved successfully.',
            'result' => $product_cat_detail,
        ];
        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductCategory $productCategory)
    {
        //
    }
}
