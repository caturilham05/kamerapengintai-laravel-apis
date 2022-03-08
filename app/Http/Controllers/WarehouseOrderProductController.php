<?php

namespace App\Http\Controllers;

use App\Models\WarehouseOrderProduct;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WarehouseOrderProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = WarehouseOrderProduct::paginate(15);
        $count = WarehouseOrderProduct::count();
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
     * @param  \App\Models\WarehouseOrderProduct  $warehouseOrderProduct
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        var_dump($id);die();
        if (empty($id))
        {
            $result = [
                'status' => 'error',
                'message' => 'data not found',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }

        $data = WarehouseOrderProduct::find($id);
        $count = WarehouseOrderProduct::count();
        if (empty($data)) {
            $result = [
                'status' => 'error',
                'message' => 'No data found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }
        $result = [
            'status' => 'success',
            'message' => sprintf('%s retrieved successfully.', $data->invoice),
            'result' => $data,
        ];
        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WarehouseOrderProduct  $warehouseOrderProduct
     * @return \Illuminate\Http\Response
     */
    public function edit(WarehouseOrderProduct $warehouseOrderProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WarehouseOrderProduct  $warehouseOrderProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WarehouseOrderProduct $warehouseOrderProduct)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WarehouseOrderProduct  $warehouseOrderProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(WarehouseOrderProduct $warehouseOrderProduct)
    {
        //
    }

    public function useParams($invoice)
    {
        if (empty($invoice)) {
            return response()->json(['message' => 'Invoice cant empty'], Response::HTTP_NOT_FOUND);
        }

        $data = WarehouseOrderProduct::where('invoice', 'like', '%' . $invoice . '%')
            ->get();

        $count_data = $data->count();
        if ($count_data == 0) {
            return response()->json(['message' => sprintf('orders like %s not found', $invoice)], Response::HTTP_NOT_FOUND);
        }

        $result = [
            'message' => sprintf('orders like %s', $invoice),
            'count_result' => $count_data,
            'result' => $data
        ];
        return response()->json($result, Response::HTTP_OK);
    }
}
