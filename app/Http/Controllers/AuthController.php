<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Models\User;

class AuthController extends Controller
{
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        if ($validator->fails()) {
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $user->save();

        return customResponse()
            ->message('Successfully created user!')
            ->data($user)
            ->success()
            ->generate();
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if ($validator->fails()) {
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return customResponse()
                ->message('These credentials do not match our records.')
                ->data(null)
                ->failed(404)
                ->generate();
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return customResponse()
            ->message('Successfully logged in!')
            ->data([
                'user' => $user,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ])
            ->success()
            ->generate();
    }

    public function logout (Request $request) {
        $request->user()->token()->revoke();
        return customResponse()
            ->message('Successfully logged out!')
            ->data(null)
            ->success()
            ->generate();
    }

    public function user (Request $request) {
        $user = $request->user();
        return customResponse()
            ->message('Success in Getting User.')
            ->data($user)
            ->success()
            ->generate();
    }
}
