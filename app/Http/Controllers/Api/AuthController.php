<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'user',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Account created successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    // account function (displays saved articles by user)
    public function account(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load('savedArticles'),
        ]);
    }

    // save article inside of "saved_articles" table (seperate per user)
    public function saveArticle(Request $request, Article $article): JsonResponse
    {
        $user = $request->user();

        $user->savedArticles()->syncWithoutDetaching([
            $article->id => ['saved_at' => now()],
        ]);

        return response()->json([
            'message' => 'Article saved successfully',
            'user' => $user->load('savedArticles'),
        ]);
    }

    //remove article inside of "saved_articles" table (seperate per user)
    public function removeSavedArticle(Request $request, Article $article): JsonResponse
    {
        $user = $request->user();

        $user->savedArticles()->detach($article->id);

        return response()->json([
            'message' => 'Article removed successfully',
            'user' => $user->load('savedArticles'),
        ]);
    }
    // update account details, can update name, email and password seperately, but username is required when updating email or password (for unique validation)
    public function updateAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'username' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $data = $request->only([
            'username',
            'name',
            'email',
        ]);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return response()->json([
            'message' => 'Account updated successfully',
            'user' => $user->fresh(),
        ]);
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out',
        ]);
    }
}
