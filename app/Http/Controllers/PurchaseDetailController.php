<?php

namespace App\Http\Controllers;

use App\Models\Purchase_detail;
use Illuminate\Http\Request;

class PurchaseDetailController extends Controller
{
    public function index()
    {
        $data = Purchase_detail::with(['purchase', 'product'])->get();

        return apiResponse($data, 200, 'get purchase details successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pur_id' => ['required', 'exists:purchases,id'],
            'pro_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $purchaseDetail = Purchase_detail::create($data);

        return apiResponse($purchaseDetail, 201, 'create purchase detail successfully');
    }

    public function show($id)
    {
        $purchaseDetail = Purchase_detail::with(['purchase', 'product'])->findOrFail($id);

        return apiResponse($purchaseDetail, 200, 'get purchase detail successfully');
    }

    public function update(Request $request, $id)
    {
        $purchaseDetail = Purchase_detail::findOrFail($id);
        $data = $request->validate([
            'pur_id' => ['sometimes', 'required', 'exists:purchases,id'],
            'pro_id' => ['sometimes', 'required', 'exists:products,id'],
            'quantity' => ['sometimes', 'required', 'integer', 'min:1'],
            'unit_cost' => ['sometimes', 'required', 'numeric', 'min:0'],
        ]);

        $purchaseDetail->update($data);

        return apiResponse($purchaseDetail, 200, 'update purchase detail successfully');
    }

    public function destroy($id)
    {
        $purchaseDetail = Purchase_detail::findOrFail($id);
        $purchaseDetail->delete();

        return apiResponse(null, 200, 'delete purchase detail successfully');
    }
}
