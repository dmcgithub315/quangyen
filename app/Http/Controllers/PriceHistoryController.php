<?php

namespace App\Http\Controllers;

use App\Models\PriceHistory;
use Illuminate\Http\Request;

class PriceHistoryController extends Controller
{
    public function getPriceHistoryById($id)
    {
        $priceHistory = PriceHistory::find($id);
        if (!$priceHistory) {
            return response()->json(['message' => 'Lịch sử giá sản phẩm không tồn tại'], 400);
        }
        return response()->json($priceHistory);
    }
    
    public function getListPriceHistory(Request $request)
    {
        $product_id = $request->product_id;
        if($product_id){
            $priceHistory = PriceHistory::where('product_id', $product_id)->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        }else{
            $priceHistory = PriceHistory::all()->orderBy('date', 'desc')->orderBy('id', 'desc');
        }
        return response()->json($priceHistory);
    }
}