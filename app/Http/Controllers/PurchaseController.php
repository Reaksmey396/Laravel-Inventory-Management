<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function index()
    {
        $data = Purchase::with(['supplier', 'user', 'creator', 'updater', 'details.product'])->get();

        return apiResponse($data, 200, 'get purchases successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_no' => ['required', 'string', 'max:255'],
            'purchase_date' => ['required', 'date'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'pur_status' => ['nullable', Rule::in(['completed', 'canceled'])],
            'pur_note' => ['required', 'string'],
        ]);
        $data['user_id'] = $request->user()->id;
        $data['created_by'] = $request->user()->id;

        $purchase = Purchase::create($data);

        return apiResponse($purchase, 201, 'create purchase successfully');
    }

    public function create(Request $request)
    {
        return $this->store($request);
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'user', 'creator', 'updater', 'details.product'])->findOrFail($id);

        return apiResponse($purchase, 200, 'get purchase successfully');
    }

    public function display($id = null)
    {
        return $id ? $this->show($id) : $this->index();
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);
        $data = $request->validate([
            'supplier_id' => ['sometimes', 'required', 'exists:suppliers,id'],
            'purchase_no' => ['sometimes', 'required', 'string', 'max:255'],
            'purchase_date' => ['sometimes', 'required', 'date'],
            'total_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'pur_status' => ['sometimes', 'required', Rule::in(['completed', 'canceled'])],
            'pur_note' => ['sometimes', 'required', 'string'],
        ]);
        $data['updated_by'] = $request->user()->id;

        $purchase->update($data);

        return apiResponse($purchase, 200, 'update purchase successfully');
    }

    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();

        return apiResponse(null, 200, 'delete purchase successfully');
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }
}
