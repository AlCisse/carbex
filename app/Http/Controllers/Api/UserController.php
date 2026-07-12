<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = auth()->user()->organization->users;
        return response()->json(['data' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        // TODO: Create user
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        // TODO: Update user
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(User $user): JsonResponse
    {
        // TODO: Delete user
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function invite(User $user): JsonResponse
    {
        // TODO: Send invitation
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function resendInvite(User $user): JsonResponse
    {
        // TODO: Resend invitation
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
