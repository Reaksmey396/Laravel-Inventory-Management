<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    private const IMAGE_RELATIONS = ['category', 'supplier', 'user', 'creator', 'updater'];

    public function index()
    {
        $data = Product::with(self::IMAGE_RELATIONS)->get();

        return apiResponse($data, 200, 'get products successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'barcode' => ['required', 'string', 'max:255', 'unique:products,barcode'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['nullable', 'integer', 'min:0'],
            'low_stock' => ['nullable', 'integer', 'min:0'],
            'pro_image' => $this->imageRules($request, 'pro_image'),
            'pro_des' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        if ($request->hasFile('pro_image')) {
            $data['pro_image'] = $this->storeImage($request, 'pro_image');
        }

        $data['user_id'] = $request->user()->id;
        $data['created_by'] = $request->user()->id;

        $product = Product::create($data)->load(self::IMAGE_RELATIONS);

        return apiResponse($product, 201, 'create product successfully');
    }

    public function create(Request $request)
    {
        return $this->store($request);
    }

    public function show($id)
    {
        $product = Product::with(self::IMAGE_RELATIONS)->findOrFail($id);

        return apiResponse($product, 200, 'get product successfully');
    }

    public function display($id = null)
    {
        return $id ? $this->show($id) : $this->index();
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validate([
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'supplier_id' => ['sometimes', 'required', 'exists:suppliers,id'],
            'product_name' => ['sometimes', 'required', 'string', 'max:255'],
            'barcode' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('products', 'barcode')->ignore($product->id)],
            'sku' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product->id)],
            'cost_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'sale_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock_qty' => ['sometimes', 'required', 'integer', 'min:0'],
            'low_stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'pro_image' => $this->imageRules($request, 'pro_image'),
            'pro_des' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive'])],
        ]);

        if ($request->hasFile('pro_image')) {
            $data['pro_image'] = $this->storeImage($request, 'pro_image');
        }

        $data['updated_by'] = $request->user()->id;

        $product->update($data);

        return apiResponse($product->load(self::IMAGE_RELATIONS), 200, 'update product successfully');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return apiResponse(null, 200, 'delete product successfully');
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    private function imageRules(Request $request, string $field): array
    {
        if ($request->hasFile($field)) {
            return ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'];
        }

        return ['nullable', 'string', 'max:255'];
    }

    private function storeImage(Request $request, string $field): string
    {
        $file = $request->file($field);
        $fileName = time().'_'.Str::random(12).'.'.$file->getClientOriginalExtension();
        $file->move(public_path('images'), $fileName);

        return url('images/'.$fileName);
    }
}
