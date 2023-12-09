<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'berhasil mendapatkan data',
            'data' => User::query()->paginate(10)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        $data = $request->only(['name', 'email', 'password']);
        $data['password'] = Hash::make($data['password']);
        $user = User::query()->create($data);
        $user->refresh();

        return response()->json([
            'message' => 'Berhasil menambahkan data',
            'data' => $user
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::query()->findOrFail($id);

        return response()->json([
            'message' => $user,
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        $user = User::query()->findOrFail($id);
        $data = $request->only(['name', 'email', 'password']);
        $data['password'] = Hash::make($data['password']);
        $user->update($data);
        $user->refresh();

        return response()->json([
            'message' => 'Berhasil mengubah data',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::query()->findOrFail($id);
        $user->delete();

        User::query()->where('id', $id)->delete();

        return response()->json([
            'message' => 'berhasil menghapus data',
            'data' => $user
        ]);
    }
}
