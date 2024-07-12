<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function update(Request $request, User $user)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'name'     => 'string',
        'password' => 'string|min:8|max:20'
    ]);

    // Handle validation errors
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Ada Kesalahan',
            'data'    => $validator->errors()
        ], 422); // Use 422 Unprocessable Entity for validation errors
    }

    // Update user fields
    $input = $request->only(['name', 'password']);
    $input['password'] = bcrypt($input['password']); // Encrypt the password
    $user->update($input); // Update the user model

    // Refresh the user model instance
    $user->refresh();

    // Return successful response
    return response()->json([
        'success' => true,
        'message' => 'Model updated successfully',
        'data'    => $user
    ], 200); // Use 200 OK for a successful update
}

public function destroy(User $user)
{
    $user->delete();

    return response()->json([
        'success' => true,
        'message' => 'Model deleted successfully'
    ], 200); // Use 200 OK for a successful deletion
}
}
