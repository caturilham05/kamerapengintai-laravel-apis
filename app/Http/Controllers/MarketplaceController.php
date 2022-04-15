<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class MarketplaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $marketplace = Marketplace::paginate(15);
        $count = Marketplace::count();
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
            'message' => 'Marketplace retrieved successfully.',
            'result' => $marketplace,
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
        $validator = Validator::make($request->all(), [
            'marketplace' => ['required'],
            'name' => ['required'],
            'store_address' => ['required'],
            'store_number_phone' => ['required'],
            'discount' => ['required', 'numeric', 'min:0'],
            'extra_ongkir' => ['required', 'numeric', 'min:0'],
            'extra_cashback' => ['required', 'numeric', 'min:0'],
            'payment_fee' => ['required', 'numeric', 'min:0'],
            'admin_fee' => ['required', 'numeric', 'min:0'],
            'discount_maximum' => ['required', 'numeric', 'min:0']
        ]);

        if ($validator->fails()) {
            $result = [
                'status' => 'error',
                'message' => 'Validation error.',
                'result' => $validator->errors(),
            ];
            return response()->json($result, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $cek_store = Marketplace::where('name', $request['name'])->get();
        $count_store = $cek_store->count();
        if ($count_store > 0) //return false if name store already exist
        {
            $result = [
                'status' => 'error',
                'message' => sprintf('Store %s already exist.', $request['name']),
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $post = Marketplace::create($request->all());
        $response = [
            'status' => 'success',
            'message' => 'Marketplace created successfully.',
            'result' => $post,
        ];
        try {
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Marketplace already exists.' . $e->errorInfo,
                    'result' => [],
                ],
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (empty($id)) {
            return response()->json(['message' => 'Data not found'], Response::HTTP_NOT_FOUND);
        }
        $marketplace = Marketplace::select(
            'id',
            'marketplace',
            'name',
            'store_address',
            'store_number_phone',
            'discount',
            'extra_ongkir',
            'extra_cashback',
            'payment_fee',
            'admin_fee',
            'discount_maximum',
            'created',
            'updated',
        )
            ->where('id', $id)
            ->get();
        $count = $marketplace->count();
        if ($count == 0) {
            return response()->json(['message' => 'Marketplace not found'], Response::HTTP_NOT_FOUND);
        }

        $result = [
            'status' => 'success',
            'message' => sprintf('%s store retrieved successfully.', $marketplace[0]->name),
            'result' => $marketplace,
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

}
