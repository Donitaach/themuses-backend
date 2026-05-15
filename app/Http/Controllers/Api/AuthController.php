<?php

namespace App\Http\Controllers\Api;

// controller
use App\Http\Controllers\Controller;

// model user
use App\Models\User;

// request
use Illuminate\Http\Request;

// auth
use Illuminate\Support\Facades\Auth;

// hash password
use Illuminate\Support\Facades\Hash;

// storage
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        // validasi input
        $request->validate([

            'name' => 'required',

            'email' => 'required|email|unique:users',

            'password' => 'required|min:6',
        ]);

        // create user
        $user = User::create([

            'name' => $request->name,

            'email' => $request->email,

            // hash password
            'password' => Hash::make($request->password),
        ]);

        // generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // response
        return response()->json([

            'success' => true,

            'user' => $user,

            'token' => $token,
        ]);
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        // validasi
        $request->validate([

            'email' => 'required|email',

            'password' => 'required',
        ]);

        // cek login
        if (!Auth::attempt($request->only('email', 'password'))) {

            return response()->json([

                'success' => false,

                'message' => 'Email atau password salah',
            ], 401);
        }

        // ambil user login
        $user = Auth::user();

        // buat token
        $token = $user->createToken('auth_token')->plainTextToken;

        // response
        return response()->json([

            'success' => true,

            'user' => $user,

            'token' => $token,
        ]);
    }

    /**
     * GET PROFILE
     */
    public function profile(Request $request)
    {
        return response()->json([

            'success' => true,

            'user' => $request->user(),
        ]);
    }

    /**
     * UPDATE PROFILE
     */
    public function updateProfile(Request $request)
    {
        // ambil user login
        $user = $request->user();

        // validasi
        $request->validate([

            'name' => 'required|string|max:255',

            'email' => 'required|email',

            'phone' => 'nullable|string',

            'address' => 'nullable|string',

            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        /**
         * upload avatar
         */
        if ($request->hasFile('avatar')) {

            /**
             * hapus avatar lama
             */
            if (
                $user->avatar &&
                Storage::disk('public')->exists($user->avatar)
            ) {

                Storage::disk('public')->delete($user->avatar);
            }

            /**
             * simpan avatar baru
             */
            $path = $request
                ->file('avatar')
                ->store('avatars', 'public');

            $user->avatar = $path;
        }

        /**
         * update data
         */
        $user->name = $request->name;

        $user->email = $request->email;

        $user->phone = $request->phone;

        $user->address = $request->address;

        $user->save();

        // response
        return response()->json([

            'success' => true,

            'message' => 'Profile berhasil diupdate',

            'user' => $user,
        ]);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        // hapus token login sekarang
        $request->user()->currentAccessToken()->delete();

        return response()->json([

            'success' => true,

            'message' => 'Logout berhasil'
        ]);
    }
}