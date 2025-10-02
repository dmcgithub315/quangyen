<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PriceHistory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    public function createProduct(Request $request)
    {
        $existingName = Product::where('name', $request->name)->first();
        if($existingName){
            return response()->json(['message' => 'Tên sản phẩm đã tồn tại'], 400);
        }
        $product = Product::create($request->all());
        return response()->json($product);
    }
    
    
    public function getProductById($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 400);
        }
        return response()->json($product);
    }
    
    public function updateProductPrice(Request $request, $id)
    {
        $newPrice = $request->new_price;
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 400);
        }
        if ($product->price != $newPrice) {
            $priceHistory = PriceHistory::create([
                'product_id' => $id,
                'old_price' => $product->price,
                'new_price' => $newPrice,
                'date' => now(),
            ]);
            $product->price = $newPrice;
            $product->save();
        } else {
            return response()->json(['message' => 'Giá sản phẩm không thay đổi'], 400);
        }
        return response()->json($product);
    }
    
    public function getListProduct(Request $request)
    {
        $inventory_limit = $request->inventory_limit;
        if($inventory_limit){
            $product = Product::where('quantity', '<=', $inventory_limit)->orderBy('quantity', 'asc')->get();
        }else{
            $product = Product::orderBy('name', 'asc')->get();
        }
        return response()->json($product);
    }
    
    public function updateProduct(Request $request, $id)
    {   
        $existingName = Product::where('name', $request->name)->first();
        if($existingName){
            return response()->json(['message' => 'Tên sản phẩm đã tồn tại'], 400);
        }
        $product = Product::find($id);
        if($request->price){
            updateProductPrice($request->price, $id);
        }
        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 400);
        }
        $product->update($request->all());
        return response()->json($product);
    }
    
    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 400);
        }
        $product->delete();
        return response()->json($product);
    }
    
    
    
}