<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    private const SUPPLIER_RELATIONS = ['user', 'creator', 'updater', 'products', 'purchases'];

    public function index()
    {
        $data = Supplier::with(self::SUPPLIER_RELATIONS)->get();

        return apiResponse($data, 200, 'get suppliers successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sup_name' => ['required', 'string', 'max:255'],
            'sup_phone' => ['required', 'string', 'max:255'],
            'sup_email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);
        $data['user_id'] = $request->user()->id;
        $data['created_by'] = $request->user()->id;

        $supplier = Supplier::create($data)->load(self::SUPPLIER_RELATIONS);

        return apiResponse($supplier, 201, 'create supplier successfully');
    }

    public function create(Request $request)
    {
        return $this->store($request);
    }

    public function show($id)
    {
        $supplier = Supplier::with(self::SUPPLIER_RELATIONS)->findOrFail($id);

        return apiResponse($supplier, 200, 'get supplier successfully');
    }

    public function display($id = null)
    {
        return $id ? $this->show($id) : $this->index();
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $data = $request->validate([
            'sup_name' => ['sometimes', 'required', 'string', 'max:255'],
            'sup_phone' => ['sometimes', 'required', 'string', 'max:255'],
            'sup_email' => ['sometimes', 'required', 'email', 'max:255'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive'])],
        ]);
        $data['updated_by'] = $request->user()->id;

        $supplier->update($data);

        return apiResponse($supplier->load(self::SUPPLIER_RELATIONS), 200, 'update supplier successfully');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return apiResponse(null, 200, 'delete supplier successfully');
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }
}
