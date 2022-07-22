<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCart;
use Faker\Core\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductCartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if ($user->id === 0) {
            return response()->json(['message' => 'unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
            'qty' => 'required|integer',
            'stock' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $cekProductCart = DB::table('kp_product_cart')->where('user_id', $request->user_id)->where('product_id', $request->product_id)->first();

        $db_results = [];
        DB::beginTransaction();
        if (!empty($cekProductCart)) {
            $prepareUpdate = [
                'qty' => $cekProductCart->qty + $request->qty
            ];
            $db_results[] = DB::table('kp_product_cart')->where('user_id', $request->user_id)->where('product_id', $request->product_id)->update($prepareUpdate);
        } else {
            $prepareCrate = [
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'qty' => $request->qty,
                'stock' => $request->stock,
            ];
            $cekProductId = DB::table('warehouse_product')->where('id', $prepareCrate['product_id'])->first();
            if (empty($cekProductId)) {
                return response()->json(['message' => 'product not found'], Response::HTTP_NOT_FOUND);
            }

            $db_results[] = ProductCart::create($prepareCrate);
        }

        if (in_array(false, $db_results)) {
            DB::rollBack();
            return response()->json(['message' => 'Failed add to cart product'], Response::HTTP_BAD_REQUEST);
        }
        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'Success add to cart product'], 200);
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
        if ($id == 0) {
            $result = [
                'status' => 'error',
                'message' => 'Product ID is Required.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }

        $user = auth()->user();
        $user_id = $user->id;
        if ($id != $user_id) {
            $result = [
                'status' => 'error',
                'message' => 'User Not Found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }

        $product_cart = ProductCart::where('user_id', $id)->orderBy('id', 'desc')->get();
        if ($product_cart == null) {
            $result = [
                'status' => 'error',
                'message' => 'Your Cart is Empty.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }

        $product_id = array_column($product_cart->toArray(), 'product_id');
        $product = Product::whereIn('id', $product_id)->get();

        if ($product == null) {
            $result = [
                'status' => 'error',
                'message' => 'Product Not Found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }

        foreach ($product as $key => $value) {
            $product_key[$value['id']] = $value;
        }

        if ($product_key == null) {
            $result = [
                'status' => 'error',
                'message' => 'Product Not Found.',
                'result' => [],
            ];
            return response()->json($result, Response::HTTP_NOT_FOUND);
        }

        $discount_all = 0;
        $grand_total = 0;
        $total_cart = 0;
        // $new_data['discount_all'] = 0;
        // $new_data['total_product'] = 0;
        // $new_data['grand_total'] = 0;
        // $new_data['total_cart'] = 0;


        foreach ($product_cart as $value) {
            $value['title'] = !empty($product_key[$value['product_id']]['title']) ? $product_key[$value['product_id']]['title'] : $product_key[$value['product_id']]['name'];
            $value['intro'] = !empty($product_key[$value['product_id']]['intro']) ? $product_key[$value['product_id']]['intro'] : '';
            $value['qty_available'] = !empty($product_key[$value['product_id']]['qty_available']) ? $product_key[$value['product_id']]['qty_available'] : 0;
            $value['sale'] = !empty($product_key[$value['product_id']]['sale']) ? $product_key[$value['product_id']]['sale'] : 0;
            $value['is_ppn'] = !empty($product_key[$value['product_id']]['is_ppn']) ? $product_key[$value['product_id']]['is_ppn'] : 0;
            $value['image'] = !empty($product_key[$value['product_id']]['image']) ? $product_key[$value['product_id']]['image'] : '';

            if ($value['is_ppn'] == 1) {
                $value['sale'] = $value['sale'] * 1.11;
            }

            $value['sale'] = ($value['sale'] == 1) ? rand(1500000, 1599999) : $value['sale'];
            $value['total_sale'] = $value['sale'] * $value['qty'];
            $value['discount'] = rand(5000, 15000);
            $discount_all += $value['discount'];
            $grand_total += $value['total_sale'];
            $total_cart += $value['qty'];

            $new_data[] = $value;
        }

        // $new_data['total']['discount_all'] = $discount_all;
        // $new_data['total']['total_product'] = $grand_total;
        // $grand_total = floor($grand_total) - floor($discount_all);
        // $new_data['total']['grand_total'] = $grand_total;
        // $new_data['total']['total_cart'] = $total_cart;

        // $results = [
        //     'discount_all' => $discount_all,
        //     'total_product' => $grand_total,
        //     'grand_total' => $grand_total,
        //     'total_cart' => $total_cart,
        //     'order_products' => $new_data,
        // ];

        $result = [
            'status' => 'success',
            'message' => 'Cart retrieved successfully.',
            'result' => $new_data,
        ];

        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
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
        $productCart = DB::table('kp_product_cart')->where('id', $id)->value('qty');
        if (empty($productCart)) {
            return response()->json(['message' => 'Product Cart not found'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'qty' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $prepareUpdate = [
            'qty' => $productCart + $request->qty,
        ];

        $db_results[] = ProductCart::where('id', $id)->update($prepareUpdate);
        return response()->json(['status' => 'success', 'message' => 'Success update cart product'], 200);
    }

    public function decrementQty(Request $request, $id)
    {
        $productCart = DB::table('kp_product_cart')->where('id', $id)->value('qty');
        if (empty($productCart)) {
            return response()->json(['message' => 'Product Cart not found'], Response::HTTP_NOT_FOUND);
        }

        if ($productCart == 1) {
            $this->destroy($id);
            return response()->json(['status' => 'success', 'message' => 'Success delete cart product', 'is_delete' => 1], 200);
        } else {
            $validator = Validator::make($request->all(), [
                'qty' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $prepareUpdate = [
                'qty' => $productCart - $request->qty,
            ];

            $db_results[] = ProductCart::where('id', $id)->update($prepareUpdate);
            return response()->json(['status' => 'success', 'message' => 'Success update cart product'], 200);
        }
    }

    // public function productCartTotal($id)
    // {
    //     $product_cart = DB::table('kp_product_cart')->where('user_id', $id)->get();
    //     if (empty($product_cart)) {
    //         return response()->json(['message' => 'Product Cart not found'], Response::HTTP_NOT_FOUND);
    //     }

    //     $product_id = array_column($product_cart->toArray(), 'product_id');
    //     $product = Product::whereIn('id', $product_id)->get();

    //     if ($product == null) {
    //         $result = [
    //             'status' => 'error',
    //             'message' => 'Product Not Found.',
    //             'result' => [],
    //         ];
    //         return response()->json($result, Response::HTTP_NOT_FOUND);
    //     }

    //     foreach ($product as $key => $value) {
    //         $product_key[$value['id']] = $value;
    //     }

    //     if ($product_key == null) {
    //         $result = [
    //             'status' => 'error',
    //             'message' => 'Product Not Found.',
    //             'result' => [],
    //         ];
    //         return response()->json($result, Response::HTTP_NOT_FOUND);
    //     }

    //     $discount_all = 0;
    //     $grand_total = 0;
    //     $new_data['discount_all'] = 0;
    //     $new_data['total_product'] = 0;
    //     $new_data['grand_total'] = 0;
    //     $total_cart = 0;

    //     foreach ($product_cart as $key => $value) {
    //         $value->sale = !empty($product_key[$value->product_id]['sale']) ? $product_key[$value->product_id]['sale'] : 0;
    //         $value->is_ppn = !empty($product_key[$value->product_id]['is_ppn']) ? $product_key[$value->product_id]['is_ppn'] : 0;
    //         $value->sale = ($value->sale == 1) ? rand(1500000, 1599999) : $value->sale;
    //         $value->sale_with_ppn = $value->is_ppn == 1 ? $value->sale * 1.11 : $value->sale;
    //         $value->total_sale = $value->sale_with_ppn * $value->qty;
    //         $value->discount = rand(5000, 15000);
    //         $discount_all += $value->discount;
    //         $grand_total += $value->total_sale;
    //         $total_cart += $value->qty;
    //     }

    //     $new_data['discount_all'] = $discount_all;
    //     $new_data['total_product'] = $grand_total;
    //     $grand_total = floor($grand_total) - floor($discount_all);
    //     $new_data['grand_total'] = $grand_total;
    //     $new_data['total_cart'] = $total_cart;

    //     $result = [
    //         'status' => 'success',
    //         'message' => 'Product Cart retrieved successfully.',
    //         'result' => $new_data,
    //     ];
    //     return response()->json($result, Response::HTTP_OK);
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productCart = DB::table('kp_product_cart')->where('id', $id)->value('qty');
        if (empty($productCart)) {
            return response()->json(['message' => 'Product Cart not found'], Response::HTTP_NOT_FOUND);
        }

        $db_results[] = ProductCart::where('id', $id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Success delete cart product'], 200);
    }
}
