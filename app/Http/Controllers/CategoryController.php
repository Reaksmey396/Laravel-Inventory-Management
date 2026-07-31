<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $data = Category::with(['user', 'creator', 'updater', 'products'])->get();

        return apiResponse($data, 200, 'get categories successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cate_name' => ['required', 'string', 'max:255'],
            'cate_des' => ['required', 'string', 'max:255'],
            'cate_status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        if($request->hasFile('cate_image')){
            $file = $request->file('cate_image');
            $fileName = $file->getClientOriginalName();
            $file->move('images', $fileName);
            // $data['cate_image'] = 'images/'.$fileName;
            $data['cate_image'] = url('images/'.$fileName);
        } else {
            $data['cate_image'] = null;
        }

        $data['user_id'] = $request->user()->id;
        $data['created_by'] = $request->user()->id;

        $category = Category::create($data);

        return apiResponse($category, 201, 'create category successfully');
    }

    public function create(Request $request)
    {
        return $this->store($request);
    }

    public function show($id)
    {
        $category = Category::with(['user', 'creator', 'updater', 'products'])->findOrFail($id);

        return apiResponse($category, 200, 'get category successfully');
    }

    public function display($id = null)
    {
        return $id ? $this->show($id) : $this->index();
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validate([
            'cate_name' => ['required', 'string'],
            'cate_des' => ['required', 'string',],
            'cate_status' => ['sometimes', 'required', Rule::in(['active', 'inactive'])],
        ]);
        if($request->hasFile('cate_image')){
            $file = $request->file('cate_image');
            $fileName = $file->getClientOriginalName();
            $file->move('images', $fileName);
            // $data['cate_image'] = 'images/'.$fileName;
            $data['cate_image'] = url('images/'.$fileName);
        } else {
            $data['cate_image'] = $category->cate_image;
        }
        $data['updated_by'] = $request->user()->id;

        $category->update($data);

        return apiResponse($category, 200, 'update category successfully');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return apiResponse(null, 200, 'delete category successfully');
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }
}
