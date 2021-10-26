<?php

namespace App\Http\Controllers\API;

use Auth;
use Hash;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    public function _construct() {
        $this->middleware('auth:api', ['except' => 'login', 'register']);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => ['email', 'required'],
            'password' => ['required', 'string', 'min:6']
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        if(!$token = Auth::attempt($validator->validated())) {
            if(method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }

            // increment the wrong login attempts
            $this->incrementLoginAttempts($request);

            return response()->json(['error' => trans('responses.login.failed.credentials')], 401);
        }

        return $this->createNewToken($token);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'min:3'],
            'email' => ['email', 'required'],
            'password' => ['required', 'min:6']
        ]);

        if($validator->passes()) {
            // validate if the email exists in the database before proceeding,
            $email = User::with('Order')->where('email', '=', $request['email'])->count();

            if($email > 0) {
                return response()->json(['status' => 'error', 'message' => trans('responses.register.failed.existing_email')], 400);
            }

            $user = new User($request->all());
            $user->password = Hash::make($request['password']);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => trans('responses.register.success.complete')
            ], 201);
        }

        return response()->json(['status' => 'error', 'message' => $validator->errors()->all()], 422);
    }

    public function logout() {
        auth()->logout();

        return response()->json(['status' => 'success', 'message' => trans('responses.logout.success')], 201);
    }

    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile() {
        return response()->json(auth()->user());
    }

    protected function createNewToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ], 201);
    }

    // for the max attemmpts
    public function maxAttempts()
    {
        return 5;
    }
}
