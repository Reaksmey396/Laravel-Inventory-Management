<?php

namespace App\Http\Controllers;

use App\Models\Stock_in_detail;
use Illuminate\Http\Request;

class StockInDetailController extends Controller
{
    public function index()
    {
        $data = Stock_in_detail::with(['stockIn', 'product'])->get();

        return apiResponse($data, 200, 'get stock in details successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'stock_in_id' => ['required', 'exists:stock_ins,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ]);
        $data['line_total'] = $data['quantity'] * $data['unit_cost'];

        $stockInDetail = Stock_in_detail::create($data);

        return apiResponse($stockInDetail, 201, 'create stock in detail successfully');
    }

    public function show($id)
    {
        $stockInDetail = Stock_in_detail::with(['stockIn', 'product'])->findOrFail($id);

        return apiResponse($stockInDetail, 200, 'get stock in detail successfully');
    }

    public function update(Request $request, $id)
    {
        $stockInDetail = Stock_in_detail::findOrFail($id);
        $data = $request->validate([
            'stock_in_id' => ['sometimes', 'required', 'exists:stock_ins,id'],
            'product_id' => ['sometimes', 'required', 'exists:products,id'],
            'quantity' => ['sometimes', 'required', 'integer', 'min:1'],
            'unit_cost' => ['sometimes', 'required', 'numeric', 'min:0'],
        ]);
        $quantity = $data['quantity'] ?? $stockInDetail->quantity;
        $unitCost = $data['unit_cost'] ?? $stockInDetail->unit_cost;
        $data['line_total'] = $quantity * $unitCost;

        $stockInDetail->update($data);

        return apiResponse($stockInDetail, 200, 'update stock in detail successfully');
    }

    public function destroy($id)
    {
        $stockInDetail = Stock_in_detail::findOrFail($id);
        $stockInDetail->delete();

        return apiResponse(null, 200, 'delete stock in detail successfully');
    }
}
