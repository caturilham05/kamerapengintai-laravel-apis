<?php

namespace App\Http\Controllers;

use App\Models\WarehouseOrder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class WarehouseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = WarehouseOrder::paginate(15);
        $count = WarehouseOrder::count();
        if ($count == 0) {
            $result = [
                'status' => 'error',
                'message' => 'No data found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }
        $response = [
            'message' => 'list orders',
            'result' => $datas
        ];

        return response()->json($response, Response::HTTP_OK);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($invoice)
    {
        if (empty($invoice)) {
            return response()->json(['message' => 'Invoice cant empty'], Response::HTTP_NOT_FOUND);
        }

        $data = WarehouseOrder::select(
            'id',
            'order_online_id',
            'invoice',
            'supervisor_user_id',
            'supervisor_name',
            'executor_user_id',
            'executor_name',
            'waybill',
            'sender',
            'recipient_id',
            'recipient',
            'recipient_phone',
            'recipient_address',
            'recipient_email',
            'discount',
            'discount_amount',
            'total_paid',
            'installment',
            'paid_leave',
            'payment_due',
            'payment_due_date',
            'payment_date',
            'product_id_return',
            'qty',
            'qty_inserted',
            'courier_id',
            'courier_name',
            'shipping_cost',
            'status',
            'paid_status',
            'ignore_ppn',
            'marketplace_id',
            'marketplace_name',
            'paid_off',
            'created',
            'updated',
        )
            ->where('invoice', 'like', '%' . $invoice . '%')
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function useId($id)
    {
        if (empty($id)) {
            return response()->json(['message' => 'Id cant empty'], Response::HTTP_NOT_FOUND);
        }

        $data = WarehouseOrder::select(
            'id',
            'order_online_id',
            'invoice',
            'supervisor_user_id',
            'supervisor_name',
            'executor_user_id',
            'executor_name',
            'waybill',
            'sender',
            'recipient_id',
            'recipient',
            'recipient_phone',
            'recipient_address',
            'recipient_email',
            'discount',
            'discount_amount',
            'total_paid',
            'installment',
            'paid_leave',
            'payment_due',
            'payment_due_date',
            'payment_date',
            'product_id_return',
            'qty',
            'qty_inserted',
            'courier_id',
            'courier_name',
            'shipping_cost',
            'status',
            'paid_status',
            'ignore_ppn',
            'marketplace_id',
            'marketplace_name',
            'paid_off',
            'created',
            'updated',
        )
            ->where('id', $id)
            ->limit(1)
            ->get();

        $count_data = $data->count();

        if ($count_data == 0) {
            return response()->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $result = [
            'message' => sprintf('order found by invoice %s', $data[0]->invoice),
            'count_result' => $count_data,
            'result' => $data
        ];

        return response()->json($result, Response::HTTP_OK);
    }
}
