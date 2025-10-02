<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function createSupplier(Request $request)
    {
        $existingName = Supplier::where('name', $request->name)->first();
        if($existingName){
            return response()->json(['message' => 'Tên nhà cung cấp đã tồn tại'], 400);
        }
        $existingPhone = Supplier::where('phone', $request->phone)->first();
        if($existingPhone){
            return response()->json(['message' => 'Số điện thoại đã tồn tại'], 400);
        }
        $supplier = Supplier::create($request->all());
        return response()->json($supplier);
    }

    public function getSupplierById($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json(['message' => 'Nhà cung cấp không tồn tại'], 400);
        }
        return response()->json($supplier);
    }
    
    public function getListSupplier(Request $request)
    {   
        $name = $request->name;
        if($name){
            $supplier = Supplier::where('name', 'like', '%'.$name.'%')->orderBy('name', 'asc')->get();
        }else{
            $supplier = Supplier::all()->orderBy('name', 'asc');
        }
        return response()->json($supplier);
    }
    
    public function updateSupplier(Request $request, $id)
    {
        $existingName = Supplier::where('name', $request->name)->first();
        if($existingName){
            return response()->json(['message' => 'Tên nhà cung cấp đã tồn tại'], 400);
        }
        $existingPhone = Supplier::where('phone', $request->phone)->first();
        if($existingPhone){
            return response()->json(['message' => 'Số điện thoại đã tồn tại'], 400);
        }
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json(['message' => 'Nhà cung cấp không tồn tại'], 400);
        }
        $supplier->update($request->all());
        return response()->json($supplier);
    }
    
}