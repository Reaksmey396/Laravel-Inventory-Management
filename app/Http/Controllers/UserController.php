<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const USER_RELATIONS = ['creator', 'updater'];

    public function register(Request $request)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'phone' => ['nullable', 'string', 'max:30'],
        'user_image' => $this->imageRules($request, 'user_image'),
        'password' => ['required', 'string', 'min:6'],
    ]);

    if ($request->hasFile('user_image')) {
        $data['user_image'] = $this->storeImage($request, 'user_image');
    }

    $data['password'] = Hash::make($data['password']);
    $data['role'] = 'staff';
    $data['status'] = 'inactive';

    $user = User::create($data);

    return apiResponse($user, 201, 'register successfully. Please wait for admin activation.');
}

    public function login(Request $request)
{
    $data = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $user = User::where('email', $data['email'])->first();

    if (!$user || !Hash::check($data['password'], $user->password)) {
        return apiResponse(null, 401, 'Invalid email or password');
    }

    if ($user->status !== 'active') {
        return apiResponse(null, 403, 'User inactive');
    }

    $token = $user->createToken($request->input('device_name', 'api-token'))->plainTextToken;

    return apiResponse([
        'user' => $user,
        'token' => $token,
        'token_type' => 'Bearer',
    ], 200, 'Login success');
}

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return apiResponse(null, 200, 'Logout success');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'user_image' => $this->imageRules($request, 'user_image'),
            'password' => ['sometimes', 'required', 'string', 'min:6'],
        ]);

        if ($request->hasFile('user_image')) {
            $data['user_image'] = $this->storeImage($request, 'user_image');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $data['updated_by'] = $user->id;
        $user->update($data);

        return apiResponse($user->fresh(), 200, 'update profile successfully');
    }

    public function getUser()
    {
        $data = User::all();
        return apiResponse($data, 200, 'getUser successfully');
    }

    public function getUserById($id)
    {
        $data = User::findOrFail($id);

        return apiResponse($data, 200, 'get data successfully');
    }

    public function userDetails()
    {
        $data = User::with([
            'categories',
            'products',
            'suppliers',
            'purchases',
            'stockIns',
            'stockOuts',
        ])->get();

        return apiResponse($data, 200, 'get user details successfully');
    }

    public function userDetailById($id)
    {
        $user = User::with([
            'categories',
            'products',
            'suppliers',
            'purchases',
            'stockIns',
            'stockOuts',
        ])->findOrFail($id);

        return apiResponse($user, 200, 'get user detail successfully');
    }

    public function index()
    {
        $data = User::with(self::USER_RELATIONS)->get();

        return apiResponse($data, 200, 'get users successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'user_image' => $this->imageRules($request, 'user_image'),
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'role' => ['nullable', Rule::in(['admin', 'manager', 'staff'])],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($request->hasFile('user_image')) {
            $data['user_image'] = $this->storeImage($request, 'user_image');
        }

        $data['password'] = Hash::make($data['password']);
        $data['created_by'] = $request->user()->id;

        $user = User::create($data)->load(self::USER_RELATIONS);

        return apiResponse($user, 201, 'create user successfully');
    }

    public function show($id)
    {
        $user = User::with(self::USER_RELATIONS)->findOrFail($id);

        return apiResponse($user, 200, 'get user successfully');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'user_image' => $this->imageRules($request, 'user_image'),
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive'])],
            'role' => ['sometimes', 'required', Rule::in(['admin', 'manager', 'staff'])],
            'password' => ['sometimes', 'required', 'string', 'min:6'],
        ]);

        if ($request->hasFile('user_image')) {
            $data['user_image'] = $this->storeImage($request, 'user_image');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $data['updated_by'] = $request->user()->id;

        $user->update($data);

        return apiResponse($user->load(self::USER_RELATIONS), 200, 'update user successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return apiResponse(null, 200, 'delete user successfully');
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
