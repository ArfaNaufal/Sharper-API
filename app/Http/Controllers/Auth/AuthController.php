<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\EmailVerificationNotification;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ktp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ktp_number' => 'nullable|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ada Kesalahan',
                'data' => $validator->errors()
            ]);
        }
    
        $input = $request->all();
    
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $image_name = time() . '.' . $image->extension();
            $image->move(public_path('images/profile'), $image_name);
            $input['profile_image'] = 'images/profile/' . $image_name;
        }

        if ($request->hasFile('ktp_image')) {
            $image = $request->file('ktp_image');
            $image_name = time() . '.' . $image->extension();
            $image->move(public_path('images/ktp'), $image_name);
            $input['ktp_image'] = 'images/ktp/' . $image_name;
        }
    
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
    
        $success['token'] = $user->createToken('auth_token')->plainTextToken;
        $success['fullname'] = $user->fullname;
        
        $user->notify(new EmailVerificationNotification());
    
        return response()->json([
            'success' => true,
            'message' => 'Registrasi Berhasil',
            'data' => $success
        ]);


    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:20',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'message' => 'Email tidak terdaftar!'
            ], 401);
        }

        if(!Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Password salah!'
            ], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        $response = [
            'message' => 'Berhasil Login!',
            'user' => $user,
            'token' => $token
        ];
        // return redirect()->to('/')->with('success', 'Berhasil Login!');
        return response()->json($response, 200);
    }

    public function getDetails() {
        $user = Auth::user();
        return response()->json(['success' => $user], 200);
    }
}
