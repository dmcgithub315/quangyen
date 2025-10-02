<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ImportHistory;
use App\Models\ImportHistoryDetail;
use App\Models\SupplierDebtLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportHistoryController extends Controller
{
    public function store(Request $request)
    {
        // json cần có:
        // {
        //     "supplier_id": 1,
        //     "date": "2025-06-13 00:00:00",
        //     "notes": "HĐ: HD015622.01 - Xuất hàng kiêm giấy nhận nợ",
        //     "items": [
        //       { "product_id": 101, "quantity": 21.28, "price": 1425760 },
        //       { "product_id": 102, "quantity": 22.04, "price": 1492800 },
        //       { "product_id": 103, "quantity": 20.40, "price": 1366800 }
        //       // ... các dòng khác trên hóa đơn
        //     ]
        //   }
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data) {
            $import = ImportHistory::create([
                'supplier_id' => $data['supplier_id'],
                'total_amount' => $data['total_amount'],
            ]);
            if (!$import) {
                return response()->json(['message' => 'Lỗi khi tạo hóa đơn nhập hàng'], 400);
            }

            $total = 0;
            foreach ($data['items'] as $line) {
                ImportHistoryDetail::create([
                    'import_history_id' => $import->id,
                    'product_id' => $line['product_id'],
                    'quantity' => $line['quantity'],
                    'price' => $line['price'],
                ]);
                $product = Product::find($line['product_id']);
                $product->quantity += $line['quantity'];
                $product->save();
                $total += $line['quantity'] * $line['price'];
            }

            if($data['total_amount'] != $total){
                return response()->json(['message' => 'Tổng số tiền không khớp'], 400);
            }

            $import->update(['total_amount' => $data['total_amount']]);

            // cập nhật sổ nợ NCC
            $supplier = Supplier::find($data['supplier_id']);
            $balanceAfter = ($supplier->total_debt ?? 0) + $data['total_amount'];
            $supplier->update(['total_debt' => $balanceAfter]);

            SupplierDebtLedger::create([
                'supplier_id' => $supplier->id,
                'import_id' => $import->id,
                'supplier_repayment_id' => null,
                'type' => 'import',
                'amount' => $data['total_amount'],
                'balance_after' => $balanceAfter,
                'note' => null,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $import->load('details'),
            ], 201);
        });
    }

    public function getListImportHistory(Request $request)
    {
        $importHistory = ImportHistory::all()->orderBy('date', 'desc')->orderBy('id', 'desc');
        if ($request->supplier_id) {
            $importHistory = $importHistory->where('supplier_id', $request->supplier_id);
        }
        if ($request->date) {
            $importHistory = $importHistory->where('date', $request->date);
        }
        if ($request->items) {
            $importHistory = $importHistory->whereHas('details', function ($query) use ($request) {
                $query->whereIn('product_id', $request->items);
            });
        }
        return response()->json($importHistory);
    }

    public function editImportHistory(Request $request, $id)
    {
        $importHistory = ImportHistory::find($id);
        if (!$importHistory) {
            return response()->json(['message' => 'Hóa đơn nhập hàng không tồn tại'], 400);
        }
        if (request()->has('supplier_id')) {
            $importHistory->supplier_id = $request->supplier_id;
            $oldSupplier = Supplier::find($importHistory->supplier_id);
            $oldSupplier->total_debt = $oldSupplier->total_debt - $importHistory->total_amount;
            $oldSupplier->save();
            $oldSupplierDebtLedger = SupplierDebtLedger::where('import_id', $importHistory->id)->first();
            $oldSupplierDebtLedger->amount = $importHistory->total_amount;
            $oldSupplierDebtLedger->balance_after = 0;
            $oldSupplierDebtLedger->note = 'Sửa hóa đơn nhập hàng';
            $oldSupplierDebtLedger->save();
            $newSupplier = Supplier::find($request->supplier_id);
            $newSupplier->total_debt = $newSupplier->total_debt + $importHistory->total_amount;
            $newSupplier->save();
            $newSupplierDebtLedger = SupplierDebtLedger::create([
                'supplier_id' => $newSupplier->id,
                'import_id' => $importHistory->id,
                'supplier_repayment_id' => null,
                'type' => 'import',
                'amount' => $importHistory->total_amount,
                'balance_after' => $newSupplier->total_debt,
                'note' => 'Sửa hóa đơn nhập hàng từ NCC ' . $oldSupplier->name . ' mã hóa đơn nhập hàng ' . $importHistory->id,
                'created_by' => auth()->id(),
            ]);
        }
        if (request()->has('date')) {
            $importHistory->date = $request->date;
        }
        if (request()->has('notes')) {
            $importHistory->notes = $request->notes;
        }
        if (request()->has('items')) {
            $importHistory->items = $request->items;
        }
        if (request()->has('total_amount')) {
            $supplierDebtLedger = SupplierDebtLedger::where('import_id', $importHistory->id)->first();
            $supplierDebtLedger->amount = $request->total_amount;
            $supplierDebtLedger->balance_after = $supplier->total_debt;
            $supplierDebtLedger->note = 'Sửa tổng tiền từ ' . $importHistory->total_amount . ' thành ' . $request->total_amount;
            $supplierDebtLedger->save();
            $supplier = Supplier::find($importHistory->supplier_id);
            $supplier->total_debt = $supplier->total_debt - $importHistory->total_amount + $request->total_amount;
            $importHistory->total_amount = $request->total_amount;
            $supplier->save();
        }
        $importHistory->update($request->all());
        return response()->json($importHistory);
    }

    public function deleteImportHistory($id)
    {
        $importHistory = ImportHistory::find($id);
        if (!$importHistory) {
            return response()->json(['message' => 'Hóa đơn nhập hàng không tồn tại'], 400);
        }
        $importHistory->delete();
        return response()->json($importHistory);
    }
}