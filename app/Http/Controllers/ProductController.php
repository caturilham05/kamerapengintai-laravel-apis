<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Product::paginate(request()->all());
        $count = Product::count();
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
            'message' => 'Product retrieved successfully.',
            'result' => $product,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (empty($id))
        {
            $result = [
                'status' => 'error',
                'message' => 'Product ID is required.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }

        $product = Product::select()
            ->where('id', $id)
            ->first();

        $count = $product->count();

        if ($count == 0) {
            $result = [
                'status' => 'error',
                'message' => 'Product not found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }

        $result = [
            'status' => 'success',
            'message' => 'Product retrieved successfully.',
            'count' => $count,
            'result' => $product,
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

    public function useParams($params)
    {
        if (empty($params))
        {
            $result = [
                'status' => 'error',
                'message' => 'params is required.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }

        $product = Product::select()
            ->where('name', 'LIKE', '%' . $params . '%')
            ->get();

        $count = $product->count();

        if ($count == 0) {
            $result = [
                'status' => 'error',
                'message' => sprintf('Product not found with name: %s', $params),
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }

        if ($count > 15){
            $product = Product::select()
                ->where('name', 'LIKE', '%' . $params . '%')
                ->paginate(15);
        }

        $result = [
            'status' => 'success',
            'message' => sprintf('Product retrieved successfully with name: %s', $params),
            'count' => $count,
            'result' => $product,
        ];

        return response()->json($result, Response::HTTP_OK);
    }
}
