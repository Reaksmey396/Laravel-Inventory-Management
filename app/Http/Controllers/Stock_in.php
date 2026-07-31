<?php

namespace App\Http\Controllers;

use App\Models\Stock_in as StockInModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Stock_in extends Controller
{
    public function index()
    {
        $data = StockInModel::with(['supplier', 'purchase', 'creator', 'updater', 'details.product'])->get();

        return apiResponse($data, 200, 'get stock ins successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reference_no' => ['required', 'string', 'max:255', 'unique:stock_ins,reference_no'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_id' => ['nullable', 'exists:purchases,id'],
            'stock_in_date' => ['required', 'date'],
            'status' => ['nullable', 'string', 'max:50'],
            'in_note' => ['nullable', 'string'],
        ]);
        $data['created_by'] = $request->user()->id;

        $stockIn = StockInModel::create($data);

        return apiResponse($stockIn, 201, 'create stock in successfully');
    }

    public function create(Request $request)
    {
        return $this->store($request);
    }

    public function show($id)
    {
        $stockIn = StockInModel::with(['supplier', 'purchase', 'creator', 'updater', 'details.product'])->findOrFail($id);

        return apiResponse($stockIn, 200, 'get stock in successfully');
    }

    public function display($id = null)
    {
        return $id ? $this->show($id) : $this->index();
    }

    public function update(Request $request, $id)
    {
        $stockIn = StockInModel::findOrFail($id);
        $data = $request->validate([
            'reference_no' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('stock_ins', 'reference_no')->ignore($stockIn->id)],
            'supplier_id' => ['sometimes', 'required', 'exists:suppliers,id'],
            'purchase_id' => ['nullable', 'exists:purchases,id'],
            'stock_in_date' => ['sometimes', 'required', 'date'],
            'status' => ['sometimes', 'required', 'string', 'max:50'],
            'in_note' => ['nullable', 'string'],
        ]);
        $data['updated_by'] = $request->user()->id;

        $stockIn->update($data);

        return apiResponse($stockIn, 200, 'update stock in successfully');
    }

    public function destroy($id)
    {
        $stockIn = StockInModel::findOrFail($id);
        $stockIn->delete();

        return apiResponse(null, 200, 'delete stock in successfully');
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }
}
