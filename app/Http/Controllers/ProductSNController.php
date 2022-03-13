<?php

namespace App\Http\Controllers;

use App\Models\ProductSN;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductSNController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = ProductSN::paginate(15);
        $count = ProductSN::count();
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
            'message' => 'Data retrieved successfully.',
            'result' => $datas,
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
     * @param  \App\Models\ProductSN  $productSN
     * @return \Illuminate\Http\Response
     */
    public function show(ProductSN $productSN)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductSN  $productSN
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductSN $productSN)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductSN  $productSN
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductSN $productSN)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductSN  $productSN
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductSN $productSN)
    {
        //
    }
}
