<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function createCustomer(Request $request)
    {
        $existingName = Customer::where('name', $request->name)->first();
        if($existingName){
            return response()->json(['message' => 'Tên khách hàng đã tồn tại'], 400);
        }
        $existingPhone = Customer::where('phone', $request->phone)->first();
        if($existingPhone){
            return response()->json(['message' => 'Số điện thoại đã tồn tại'], 400);
        }
        $customers = Customer::create($request->all());
        return response()->json($customers);
    }

    public function getCustomerById($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Khách hàng không tồn tại'], 400);
        }
        return response()->json($customer);
    }
    
    public function getListCustomer(Request $request)
    {   
        $name = $request->name;
        if($name){
            $customers = Customer::where('name', 'like', '%'.$name.'%')->orderBy('name', 'asc')->get();
        }else{
            $customers = Customer::all()->orderBy('name', 'asc');
        }
        return response()->json($customers);
    }
    
    public function updateCustomer(Request $request, $id)
    {
        $existingName = Customer::where('name', $request->name)->first();
        if($existingName){
            return response()->json(['message' => 'Tên khách hàng đã tồn tại'], 400);
        }
        $existingPhone = Customer::where('phone', $request->phone)->first();
        if($existingPhone){
            return response()->json(['message' => 'Số điện thoại đã tồn tại'], 400);
        }
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Khách hàng không tồn tại'], 400);
        }
        $customer->update($request->all());
        return response()->json($customer);
    }
    
}