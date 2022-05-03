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
        $product_cat = ProductCategory::select()->orderBy('id', 'DESC')->get();
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
    public function group_category()
    {
        $product_cat = ProductCategory::select()->where('par_id', 0)->where('publish', 1)->orderBy('id', 'DESC')->get();
        $count = count($product_cat);
        if ($count == 0) {
            $result = [
                'status' => 'error',
                'message' => 'No data found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }

        foreach ($product_cat as $key => $value) {
            $new_product_cat[$value['id']] = $value;
        }

        $par_id = array_column($product_cat->toArray(), 'id');
        $par_id = array_unique($par_id);
        $category_par_id = ProductCategory::select()->whereIn('par_id', $par_id)->orderBy('id', 'DESC')->get();
        $count_category_par_id = count($category_par_id);
        if ($count_category_par_id == 0) {
            $result = [
                'status' => 'error',
                'message' => 'No data found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }


        foreach ($category_par_id as $key => $value) {
            $new_category_par_id[$value['par_id']][] = $value;
        }
        foreach ($new_product_cat as $key => $value) {
            $value['grouping_par_id'] = !empty($new_category_par_id[$key]) ? $new_category_par_id[$key] : [];
            $final_grouping_category[] = $value;
        }
        $result = [
            'status' => 'success',
            'message' => 'Product Category retrieved successfully.',
            'count' => $count,
            'result' => $final_grouping_category,
        ];

        return response()->json($result, Response::HTTP_OK);
    }
}
