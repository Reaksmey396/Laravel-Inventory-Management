<?php

namespace App\Http\Controllers;

use App\Models\Stock_out;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockOutController extends Controller
{
    private const STATUSES = ['completed', 'Pending', 'Cancel'];

    public function index()
    {
        $data = Stock_out::with(['user', 'creator', 'updater', 'details.product'])->get();

        return apiResponse($data, 200, 'get stock outs successfully');
    }

    public function store(Request $request)
    {
        $this->normalizeStatus($request);

        $data = $request->validate([
            'stock_out_date' => ['required', 'date'],
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'reason' => ['nullable', 'string'],
            'reference_no' => ['required', 'string', 'max:255', 'unique:stock_outs,reference_no'],
            'out_note' => ['nullable', 'string'],
        ]);
        $data['user_id'] = $request->user()->id;
        $data['created_by'] = $request->user()->id;

        $stockOut = Stock_out::create($data);

        return apiResponse($stockOut, 201, 'create stock out successfully');
    }

    public function create(Request $request)
    {
        return $this->store($request);
    }

    public function show($id)
    {
        $stockOut = Stock_out::with(['user', 'creator', 'updater', 'details.product'])->findOrFail($id);

        return apiResponse($stockOut, 200, 'get stock out successfully');
    }

    public function display($id = null)
    {
        return $id ? $this->show($id) : $this->index();
    }

    public function update(Request $request, $id)
    {
        $stockOut = Stock_out::findOrFail($id);
        $this->normalizeStatus($request);

        $data = $request->validate([
            'stock_out_date' => ['sometimes', 'required', 'date'],
            'status' => ['sometimes', 'required', Rule::in(self::STATUSES)],
            'reason' => ['nullable', 'string'],
            'reference_no' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('stock_outs', 'reference_no')->ignore($stockOut->id)],
            'out_note' => ['nullable', 'string'],
        ]);
        $data['updated_by'] = $request->user()->id;

        $stockOut->update($data);

        return apiResponse($stockOut, 200, 'update stock out successfully');
    }

    public function destroy($id)
    {
        $stockOut = Stock_out::findOrFail($id);
        $stockOut->delete();

        return apiResponse(null, 200, 'delete stock out successfully');
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    private function normalizeStatus(Request $request): void
    {
        if (! $request->has('status') || $request->input('status') === null) {
            return;
        }

        $statuses = [
            'completed' => 'completed',
            'pending' => 'Pending',
            'cancel' => 'Cancel',
        ];

        $status = strtolower(trim((string) $request->input('status')));

        if (isset($statuses[$status])) {
            $request->merge(['status' => $statuses[$status]]);
        }
    }
}
