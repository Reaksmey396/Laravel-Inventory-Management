<?php

namespace App\Http\Controllers;

use App\Models\Stock_out_detail;
use Illuminate\Http\Request;

class StockOutDetailController extends Controller
{
    public function index()
    {
        $data = Stock_out_detail::with(['stockOut', 'product'])->get();

        return apiResponse($data, 200, 'get stock out details successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'stock_out_id' => ['required', 'exists:stock_outs,id'],
            'pro_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'out_cost' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string'],
        ]);

        $stockOutDetail = Stock_out_detail::create($data);

        return apiResponse($stockOutDetail, 201, 'create stock out detail successfully');
    }

    public function show($id)
    {
        $stockOutDetail = Stock_out_detail::with(['stockOut', 'product'])->findOrFail($id);

        return apiResponse($stockOutDetail, 200, 'get stock out detail successfully');
    }

    public function update(Request $request, $id)
    {
        $stockOutDetail = Stock_out_detail::findOrFail($id);
        $data = $request->validate([
            'stock_out_id' => ['sometimes', 'required', 'exists:stock_outs,id'],
            'pro_id' => ['sometimes', 'required', 'exists:products,id'],
            'quantity' => ['sometimes', 'required', 'integer', 'min:1'],
            'out_cost' => ['sometimes', 'required', 'numeric', 'min:0'],
            'reason' => ['sometimes', 'required', 'string'],
        ]);

        $stockOutDetail->update($data);

        return apiResponse($stockOutDetail, 200, 'update stock out detail successfully');
    }

    public function destroy($id)
    {
        $stockOutDetail = Stock_out_detail::findOrFail($id);
        $stockOutDetail->delete();

        return apiResponse(null, 200, 'delete stock out detail successfully');
    }
}
