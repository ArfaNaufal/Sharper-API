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
    
    $validator = Validator::make($request->all(), [
        'fullname'     => 'string',
        'username'     => 'string',
        'profile_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'password'      => 'string|min:8|max:20'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Ada Kesalahan',
            'data'    => $validator->errors()
        ], 422); 
    }

    $input = $request->all();

    if ($request->hasFile('profile_image')) {
        $image = $request->file('profile_image');
        $image_name = time() . '.' . $image->extension();
        $image->move(public_path('images/profile'), $image_name);
        $input['profile_image'] = 'images/profile/' . $image_name;
    }

    if (isset($input['password'])) {
        $input['password'] = bcrypt($input['password']); 
    } else {
        unset($input['password']);
    }

    $user->update($input); 

    return response()->json([
        'success' => true,
        'message' => 'Model updated successfully',
        'data'    => $user
    ], 200); 
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
