<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCart;
use Illuminate\Http\Request;
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

    $product_cart = ProductCart::where('user_id', $id)->get();
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

      $product_cart_arr[] = $value;
    }

    $result = [
      'status' => 'success',
      'message' => 'Cart retrieved successfully.',
      'result' => $product_cart_arr,
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
