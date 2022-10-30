<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCart;
use App\Models\Recipient;
use App\Models\RecipientAddress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Helper\Helper;
use App\Models\Courier;
use App\Models\WarehouseOrder;
use Illuminate\Support\Facades\DB;

class ProductCartCheckoutController extends Controller
{
    private function payment_method($id)
    {
        $user_email = User::select('email')->where('id', $id)->first();
        if (empty($user_email)) return false;
        $recipient = Recipient::select('credit_limit', 'remaining_credit_limit')->where('email', $user_email->email)->first();
        if (empty($recipient)) return false;

        $datas = [
            [
                'id' => 1,
                'title' => 'Cash',
                'text' => 'BCA',
                'type' => 1,
                'limit' => 0
            ],
            [
                'id' => 2,
                'title' => 'COD',
                'text' => 'Bayar setelah barang sampai',
                'type' => 2,
                'limit' => 0
            ],
            [
                'id' => 3,
                'title' => 'Tempo',
                'text' => '',
                'type' => 3,
                'limit' => $recipient->remaining_credit_limit
            ]
        ];
        return $datas;
    }

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
        if ($user->id == 0) return response()->json(['status' => 'error', 'message' => 'unauthorized', 'result' => []], Response::HTTP_UNAUTHORIZED);

        $invoice_generate = Helper::invoice('KPI/PJ/', 'WarehouseOrder', true);

        $create_validator = [
            'address_id' => 'required|integer',
            'cart_ids' => 'required|json',
            'shipping_name' => 'required|string',
            'payment_id' => 'required|integer'
        ];
        $validator = Validator::make($request->all(), $create_validator);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $recipient = Recipient::select('id', 'owner', 'phone', 'email', 'address')->where('email', 'like', '%' . addslashes($user->email) . '%')->first();
        if (empty($recipient)) return response()->json(['status' => 'error', 'message' => 'pengguna tidak ditemukan', 'result' => []], RESPONSE::HTTP_NOT_FOUND);

        $cart_ids = json_decode($request->cart_ids);
        $carts = ProductCart::select('id', 'product_id', 'qty', 'stock')->whereIn('id', $cart_ids)->get()->toArray();
        if (empty($carts)) return response()->json(['status' => 'error', 'message' => 'barang yang ingin anda beli tidak ditemukan', 'result' => []], RESPONSE::HTTP_NOT_FOUND);

        $product_ids = array_column($carts, 'product_id');
        $products = Product::select('id', 'name', 'sku', 'price', 'sale', 'type', 'cat_id', 'cat_ids', 'cat_name', 'position_id', 'position_name', 'is_ppn')->whereIn('id', $product_ids)->get()->toArray();

        foreach ($carts as $key => $value) {
            $cart_product_id[$value['product_id']] = $value;
        }

        foreach ($products as $key => $value) {
            $value['qty_order'] = $cart_product_id[$value['id']]['qty'];
            if ($value['is_ppn'] == 1) {
                if ($request->use_fp == 1) {
                    print_r('fp');
                    $invoice_generate = Helper::invoice('KPI/PJ/', 'WarehouseOrder', true);
                } else {
                    print_r('no fp');
                    $invoice_generate = Helper::invoice('KPI/GG/', 'WarehouseOrder', true);
                }
            } else {
                $invoice_generate = Helper::invoice('KPI/NP/', 'WarehouseOrder', true);
            }
            $value['invoice'] = $invoice_generate;
            $products[$key] = $value;
        }


        return $products;

        $stocks = array_column($carts, 'stock');
        if (in_array(0, $stocks)) return response()->json(['status' => 'error', 'message' => 'stok barang yang ingin anda beli ada yang kosong, silahkan pilih barang yang lain', 'result' => []], RESPONSE::HTTP_NOT_FOUND);

        $recipient_address = RecipientAddress::select('phone', 'location_name', 'description')->where('id', $request->address_id)->first();
        if (empty($recipient_address)) return response()->json(['status' => 'error', 'message' => 'alamat tidak ditemukan', 'result' => []], RESPONSE::HTTP_NOT_FOUND);

        $courier = Courier::select('id')->where('name', 'like', '%' . addslashes($request->shipping_name) . '%')->first();

        $prepare_post = [
            'invoice' => $invoice_generate,
            'recipient_id' => $recipient->id,
            'recipient' => $recipient->owner,
            'recipient_phone' => empty($recipient->phone) ? $recipient_address->phone : $recipient->phone,
            'recipient_address' => empty($recipient->address) ? $recipient_address->location_name . ' (' . $recipient_address->description . ')' : $recipient->address,
            'recipient_email' => $recipient->email,
            'courier_id' => empty($courier) ? 0 : $courier->id,
            'courier_name' => $request->shipping_name,
            'date' => date('Y-m-d')
        ];

        // $order_id = WarehouseOrder::create($prepare_post);
        $order_id = 58834;
        if (empty($order_id)) return response()->json(['status' => 'error', 'message' => 'order gagal diproses, silahkan coba lagi', 'result' => []], RESPONSE::HTTP_NOT_ACCEPTABLE);
        // $order = WarehouseOrder::select()->where('id', $order_id->id)->first();
        $order = WarehouseOrder::select()->where('id', $order_id)->first();
        if (empty($order)) return response()->json(['status' => 'error', 'message' => 'order tidak ditemukan', 'result' => []], RESPONSE::HTTP_NOT_FOUND);
        DB::table('kp_recipient')->where('id', $recipient->id)->update(['order_last_date' => date('Y-m-d H:i:s')]);
        $prepare_post_order_products = [
            // 'order_id' => $order_id->id,
            'order_id' => $order_id,
            'invoice' => $invoice_generate,
        ];
        return [$request->cart_ids, $order];
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
    public function show($id) //user id
    {
        $user_address = RecipientAddress::select('id', 'user_id', 'name', 'phone', 'location_id', 'location_name', 'description', 'address', 'address_primary', 'zipcode', 'store_type', 'created')->where('user_id', $id)->where('address_primary', 1)->first();
        if (empty($user_address)) {
            $datas = [
                'status' => 'error',
                'message' => 'address not found',
                'result' => []
            ];
            return response()->json($datas, Response::HTTP_NOT_FOUND);
        }

        $carts = ProductCart::select()->where('user_id', $user_address->user_id)->orderBy('id', 'DESC')->get()->toArray();

        if (empty($carts)) {
            $datas = [
                'status' => 'error',
                'message' => 'carts not found',
                'result' => []
            ];
            return response()->json($datas, Response::HTTP_NOT_FOUND);
        }

        $product_ids = array_column($carts, 'product_id');
        $product_id = array_unique($product_ids);
        $products = Product::select('id', 'title', 'sale', 'is_ppn')->whereIn('id', $product_id)->orderBy('id', 'DESC')->get()->toArray();

        if (empty($products)) {
            $datas = [
                'status' => 'error',
                'message' => 'products not found',
                'result' => []
            ];
            return response()->json($datas, Response::HTTP_NOT_FOUND);
        }

        foreach ($products as $key => $value) {
            $value['sale'] = $value['is_ppn'] == 1 ? $value['sale'] * 1.11 : $value['sale'];
            $product_assoc[$value['id']]['title'] = $value['title'];
            $product_assoc[$value['id']]['sale'] = $value['sale'];
            $product_assoc[$value['id']]['is_ppn'] = $value['is_ppn'];
        }

        $discount_all = 0;
        $grand_total = 0;
        $total_cart = 0;
        foreach ($carts as $key => $value) {
            $value['sale'] = ($product_assoc[$value['product_id']]['sale'] == 1) ? rand(1500000, 1599999) : $product_assoc[$value['product_id']]['sale'];
            $value['discount'] = rand(5000, 15000);
            $value['sale_total'] = $product_assoc[$value['product_id']]['sale'] * $value['qty'];
            $value['products'] = $product_assoc[$value['product_id']];
            $discount_all += $value['discount'];
            $grand_total += $value['sale_total'];
            $total_cart += $value['qty'];
            $cart_assoc[] = $value;
        }

        $user_address_list = RecipientAddress::select()->where('user_id', $id)->get()->toArray();
        if (empty($user_address_list)) {
            $datas = [
                'status' => 'error',
                'message' => 'the user address is still empty, please add the user address first',
                'result' => []
            ];
            return response()->json($datas, Response::HTTP_NOT_FOUND);
        }

        $user_email = User::select('email')->where('id', $id)->first();
        $recipient = Recipient::select('credit_limit', 'remaining_credit_limit')->where('email', $user_email->email)->first();
        $user_address['remaining_credit_limit'] = $recipient->remaining_credit_limit;
        $payment_method = $this->payment_method($id);

        $datas = [
            'status' => 'success',
            'message' => 'data retrieved successfully',
            'result' => [
                'user_address' => $user_address,
                'user_address_list' => $user_address_list,
                'carts' => $cart_assoc,
                'payment_method' => $payment_method,
                'total_cart' => $total_cart,
                'discount' => $discount_all,
                'grand_total' => $grand_total,
            ]
        ];


        return response()->json($datas, Response::HTTP_OK);
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
        $recipient_address_id = RecipientAddress::select('user_id')->where('id', $id)->first();
        if (empty($recipient_address_id)) {
            $datas = [
                'status' => 'error',
                'message' => 'recipient address not found',
                'result' => []
            ];
            return response()->json($datas, Response::HTTP_NOT_FOUND);
        }

        $address_primary_reset = RecipientAddress::where(['user_id' => $recipient_address_id->user_id, 'address_primary' => 1])->update(['address_primary' => 0]);
        if (empty($address_primary_reset)) {
            $datas = [
                'status' => 'error',
                'message' => 'failed update address',
                'result' => []
            ];
            return response()->json($datas, Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'address_primary' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $prepareUpdate = [
            'address_primary' => $request->address_primary,
        ];

        $address_primary_set = RecipientAddress::where(['id' => $id, 'address_primary' => 0])->update($prepareUpdate);
        if (empty($address_primary_set)) {
            $datas = [
                'status' => 'error',
                'message' => 'failed update address',
                'result' => []
            ];
            return response()->json($datas, Response::HTTP_NOT_FOUND);
        }
        $datas = [
            'status' => 'success',
            'message' => 'success update address',
            'result' => []
        ];
        return response()->json($datas, Response::HTTP_OK);
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
