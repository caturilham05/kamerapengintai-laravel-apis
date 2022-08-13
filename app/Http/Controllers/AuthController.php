<?php

namespace App\Http\Controllers;

use App\Models\BbcUser;
use App\Models\Recipient;
use App\Models\TokenUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $getUser = BbcUser::where('username', $request->email)->first();
        if ($getUser) {
            return response()->json([
                'message' => 'User already exists'
            ], Response::HTTP_BAD_REQUEST);
        }

        $validator = Validator::make($request->all(), [
            'group_ids' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $user = User::create([
            'group_ids' => $request->group_ids,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        BbcUser::create([
            'group_ids' => $request->group_ids,
            'username' => $user->email,
            'password' => $user->password,
        ]);
        Recipient::create([
            'owner' => $request->owner,
            'email' => $request->email,
        ]);
        return response()->json(['data' => $user], 200);
    }

    public function login(Request $login)
    {
        if (!Auth::attempt($login->only('email', 'password'))) {
            return response()->json(['message' => 'unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        $user = User::where('email', $login['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer', 'status' => 'success'], 200);
    }

    public function logout()
    {
        $id = Auth::user();
        $token = TokenUser::where('tokenable_id', $id->id)->delete();
        if ($token) {
            return response()->json(['message' => 'logout successfully'], 200);
        } else {
            return response()->json(['message' => 'logout failed'], 400);
        }
    }
}
